<?php

namespace App\Http\Controllers;

use App\Models\Order;
use App\Models\OrderItem;
use App\Models\Product;
use App\Models\UserAddress;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Str;

class CheckoutController extends Controller
{
    public function index()
    {
        /** @var \App\Models\User $user */
        $user = Auth::user();
        $cart = $user->getCurrentCart();

        $addresses = $user->addresses()
            ->orderByDesc('is_default')
            ->orderByDesc('created_at')
            ->get();

        $selectedAddressId = session('checkout_address_id');
        $activeAddress = $selectedAddressId
            ? $addresses->firstWhere('id', (int) $selectedAddressId)
            : null;

        if (!$activeAddress && $addresses->isNotEmpty()) {
            $activeAddress = $addresses->firstWhere('is_default', true) ?? $addresses->first();
            if ($activeAddress) {
                session([
                    'checkout_address_id' => $activeAddress->id,
                    'checkout_address' => $activeAddress->toShippingArray(),
                ]);
            }
        }

        if (!$activeAddress) {
            session()->forget(['checkout_address_id', 'checkout_address']);
        }

        $buyNow = null;
        if (session()->has('buy_now')) {
            $buyNow = session('buy_now');

            if (isset($buyNow['selected_attributes']) && is_string($buyNow['selected_attributes'])) {
                $decoded = json_decode($buyNow['selected_attributes'], true);
                $buyNow['selected_attributes'] = is_array($decoded) ? $decoded : [];
            }

            $summary = collect($buyNow['selected_attributes'] ?? [])
                ->filter(fn ($value) => $value !== null && $value !== '')
                ->map(function ($value, $key) {
                    $label = ucwords(str_replace(['_', '-'], ' ', (string) $key));
                    return $label . ': ' . $value;
                })
                ->values()
                ->implode(' · ');

            if (!empty($summary)) {
                $buyNow['variant_summary'] = $summary;
            }
        }

        $hasItems = $buyNow !== null || ($cart && $cart->items->isNotEmpty());
        if (!$hasItems) {
            return redirect()->route('cart.overview')->with('error', 'Keranjang kosong!');
        }

        return view('checkout.review', [
            'cart' => $cart,
            'buyNow' => $buyNow,
            'addresses' => $addresses,
            'activeAddress' => $activeAddress,
        ]);
    }

    public function saveAddress(Request $request)
    {
        $request->validate([
            'address_id' => 'nullable|integer',
            'label' => 'nullable|string|max:50',
            'receiver_name' => ['required', 'string', 'max:100', 'regex:/^[\pL\s]+$/u'],
            'phone' => ['required', 'string', 'max:30', 'regex:/^[0-9]+$/'],
            'address_line' => 'required|string|max:255',
            'city' => ['required', 'string', 'max:100', 'regex:/^[\pL\s]+$/u'],
            'postal_code' => ['nullable', 'string', 'max:20', 'regex:/^[0-9]*$/'],
            'set_as_default' => 'nullable|accepted',
        ], [
            'receiver_name.regex' => 'Nama penerima hanya boleh berisi huruf dan spasi.',
            'phone.regex' => 'Nomor telepon hanya boleh berisi angka.',
            'city.regex' => 'Nama kota hanya boleh berisi huruf dan spasi.',
            'postal_code.regex' => 'Kode pos hanya boleh berisi angka.',
        ]);

        /** @var \App\Models\User $user */
        $user = Auth::user();

        $payload = $request->only(['label', 'receiver_name', 'phone', 'address_line', 'city', 'postal_code']);
        $addressId = $request->input('address_id');
        $makeDefault = $request->boolean('set_as_default');

        if ($addressId) {
            $address = $user->addresses()->findOrFail($addressId);
            $address->update($payload);
        } else {
            $address = $user->addresses()->create($payload + ['is_default' => false]);
        }

        if ($makeDefault) {
            $user->addresses()->where('id', '!=', $address->id)->update(['is_default' => false]);
            $address->forceFill(['is_default' => true])->save();
        } elseif (!$user->addresses()->where('is_default', true)->exists()) {
            $user->addresses()->where('id', '!=', $address->id)->update(['is_default' => false]);
            $address->forceFill(['is_default' => true])->save();
        }

        session([
            'checkout_address_id' => $address->id,
            'checkout_address' => $address->toShippingArray(),
        ]);

        return redirect()->route('checkout.review')->with('success', 'Alamat berhasil disimpan!');
    }

    public function selectAddress(Request $request)
    {
        $request->validate([
            'address_id' => 'required|exists:user_addresses,id',
            'action' => 'nullable|in:select,make_default',
        ]);

        /** @var \App\Models\User $user */
        $user = Auth::user();
        $address = $user->addresses()->findOrFail($request->address_id);

        if ($request->input('action') === 'make_default') {
            $user->addresses()->where('id', '!=', $address->id)->update(['is_default' => false]);
            $address->forceFill(['is_default' => true])->save();
            $message = 'Alamat default diperbarui.';
        } else {
            $message = 'Alamat pengiriman dipilih.';
        }

        session([
            'checkout_address_id' => $address->id,
            'checkout_address' => $address->toShippingArray(),
        ]);

        return redirect()->route('checkout.review')->with('success', $message);
    }

    public function store(Request $request)
    {
        $request->validate([
            'payment_method' => 'required|in:gopay,shopeepay,qris,va',
        ]);

        /** @var \App\Models\User $user */
        $user = Auth::user();
        $addressId = session('checkout_address_id');
        $address = $addressId ? $user->addresses()->find($addressId) : null;

        if (!$address) {
            return redirect()->route('checkout.review')
                ->with('error', 'Pilih atau tambahkan alamat pengiriman terlebih dahulu!');
        }

        $shippingAddress = $address->toShippingArray();
        if ($address->label) {
            $shippingAddress['label'] = $address->label;
        }

        if (session()->has('buy_now')) {
            $buyNow = session('buy_now');

            $orderNumber = 'VV-' . now()->format('ymdHis') . '-U' . $user->id;

            $order = Order::create([
                'order_number'     => $orderNumber,
                'user_id'          => $user->id,
                'subtotal'         => $buyNow['subtotal'],
                'grand_total'      => $buyNow['subtotal'],
                'total_amount'     => $buyNow['subtotal'],
                'status'           => 'pending',
                'payment_method'   => $request->payment_method,
                'payment_status'   => 'unpaid',
                'shipping_address' => $shippingAddress,
                'expires_at'       => now()->addHour(),
            ]);

            OrderItem::create([
                'order_id'      => $order->id,
                'product_id'    => $buyNow['product_id'],
                'product_name'  => $buyNow['product_name'],
                'quantity'      => $buyNow['quantity'],
                'unit_price'    => $buyNow['price'],
                'subtotal'      => $buyNow['subtotal'],
                'product_price' => $buyNow['price'],
                'total_price'   => $buyNow['subtotal'],
                'selected_attributes' => $buyNow['selected_attributes'] ?? null,
            ]);

            Product::find($buyNow['product_id'])->decrement('stock', $buyNow['quantity']);

            session()->forget('buy_now');

            return redirect()->route('checkout.payment', ['order' => $order->id]);
        }

        $cart = $user->getCurrentCart();
        if (!$cart || $cart->items->isEmpty()) {
            return redirect()->route('cart.overview')->with('error', 'Keranjang kosong!');
        }

        $order = DB::transaction(function () use ($user, $cart, $request, $shippingAddress) {
            $orderNumber = 'VV-' . now()->format('ymdHis') . '-U' . $user->id;

            $order = Order::create([
                'order_number'     => $orderNumber,
                'user_id'          => $user->id,
                'subtotal'         => $cart->subtotal,
                'grand_total'      => $cart->subtotal,
                'total_amount'     => $cart->subtotal,
                'status'           => 'pending',
                'payment_method'   => $request->payment_method,
                'payment_status'   => 'unpaid',
                'shipping_address' => $shippingAddress,
                'expires_at'       => now()->addHour(),
            ]);

            foreach ($cart->items as $item) {
                OrderItem::create([
                    'order_id'      => $order->id,
                    'product_id'    => $item->product_id,
                    'product_name'  => $item->product->name,
                    'quantity'      => $item->quantity,
                    'unit_price'    => $item->unit_price,
                    'subtotal'      => $item->subtotal,
                    'product_price' => $item->unit_price,
                    'total_price'   => $item->subtotal,
                    'selected_attributes' => $item->product_attributes,
                ]);

                $item->product->decrement('stock', $item->quantity);
            }

            $cart->items()->delete();
            $cart->delete();

            return $order;
        });

        return redirect()->route('checkout.payment', ['order' => $order->id]);
    }

    public function payment(Order $order)
    {
        if ($order->user_id !== Auth::id()) {
            abort(403);
        }

        $remainingSeconds = null;
        $isExpired = false;

        if ($order->expires_at) {
            $difference = now()->diffInSeconds($order->expires_at, false);
            $remainingSeconds = $difference > 0 ? $difference : 0;
            $isExpired = $difference <= 0 && $order->payment_status !== 'paid';
        }

        return view('checkout.payment', [
            'order' => $order,
            'remainingSeconds' => $remainingSeconds,
            'isExpired' => $isExpired,
        ]);
    }

    public function completePayment(Request $request, Order $order)
    {
        if ($order->user_id !== Auth::id()) {
            abort(403);
        }

        if ($order->payment_status === 'paid') {
            return response()->json([
                'success' => true,
                'message' => 'Pesanan sudah dibayar.',
                'status' => $order->status,
                'payment_status' => $order->payment_status,
            ]);
        }

        if ($order->expires_at && now()->greaterThan($order->expires_at)) {
            return response()->json([
                'success' => false,
                'message' => 'Batas waktu pembayaran telah berakhir. Hubungi dukungan untuk bantuan.',
            ], 422);
        }

        $order->forceFill([
            'payment_status' => 'paid',
            'status' => 'processing',
        ])->save();

        return response()->json([
            'success' => true,
            'message' => 'Pembayaran berhasil dikonfirmasi.',
            'status' => $order->status,
            'payment_status' => $order->payment_status,
        ]);
    }

    public function deleteAddress(UserAddress $address)
    {
        /** @var \App\Models\User $user */
        $user = Auth::user();

        if ($address->user_id !== $user->id) {
            abort(403);
        }

        $deletedId = $address->id;
        $wasDefault = $address->is_default;

        $address->delete();

        $replacement = $user->addresses()
            ->orderByDesc('is_default')
            ->orderByDesc('created_at')
            ->first();

        if ($wasDefault && $replacement && !$replacement->is_default) {
            $replacement->forceFill(['is_default' => true])->save();
        }

        $selectedId = session('checkout_address_id');

        if ($replacement) {
            if ((int) $selectedId === $deletedId || $selectedId === null) {
                session([
                    'checkout_address_id' => $replacement->id,
                    'checkout_address' => $replacement->toShippingArray(),
                ]);
            }
        } else {
            session()->forget(['checkout_address_id', 'checkout_address']);
        }

        return redirect()
            ->route('checkout.review')
            ->with('success', 'Alamat berhasil dihapus.');
    }

    public function buyNow(Request $request, Product $product)
    {
        $request->validate([
            'quantity' => 'nullable|integer|min:1',
            'variants_payload' => 'nullable|string',
        ]);

        $quantity = max((int) $request->input('quantity', 1), 1);

        $selectedVariants = [];
        $payload = $request->input('variants_payload');
        if ($payload) {
            $decoded = json_decode($payload, true);
            if (is_array($decoded)) {
                foreach ($decoded as $key => $value) {
                    if (is_scalar($key) && (is_scalar($value) || $value === null)) {
                        $normalizedKey = trim((string) $key);
                        $normalizedValue = $value === null ? null : trim((string) $value);
                        if ($normalizedKey !== '' && $normalizedValue !== null && $normalizedValue !== '') {
                            $selectedVariants[$normalizedKey] = $normalizedValue;
                        }
                    }
                }
            }
        }

        $variantGroups = collect($product->variants ?? []);
        if ($variantGroups->isNotEmpty()) {
            $missingSelection = $variantGroups->first(function ($options, $groupKey) use ($selectedVariants) {
                $optionsList = collect($options)->map(fn ($option) => (string) $option)->all();
                $chosen = $selectedVariants[$groupKey] ?? null;

                return !in_array($chosen, $optionsList, true);
            });

            if ($missingSelection !== null) {
                return redirect()->back()
                    ->withInput()
                    ->with('error', 'Silakan pilih varian lengkap sebelum melanjutkan.');
            }
        }

        $variantSummary = collect($selectedVariants)
            ->map(function ($value, $key) {
                $label = ucwords(str_replace(['_', '-'], ' ', $key));
                return $label . ': ' . $value;
            })
            ->values()
            ->implode(' · ');

        $rawImage = collect($product->images ?? [])->first();
        if ($rawImage) {
            if (Str::startsWith($rawImage, ['http://', 'https://'])) {
                $imageUrl = $rawImage;
            } else {
                $cleanPath = ltrim($rawImage, '/');
                if (Storage::disk('public')->exists($cleanPath)) {
                    $imageUrl = asset('storage/' . $cleanPath);
                } elseif (file_exists(public_path($cleanPath))) {
                    $imageUrl = asset($cleanPath);
                } else {
                    $imageUrl = null;
                }
            }
        } else {
            $imageUrl = null;
        }

        $imageUrl = $imageUrl ?? asset('images/placeholder_img.jpg');

        session([
            'buy_now' => [
                'product_id'           => $product->id,
                'product_name'         => $product->name,
                'price'                => $product->price,
                'quantity'             => $quantity,
                'subtotal'             => $product->price * $quantity,
                'selected_attributes'  => $selectedVariants,
                'variant_summary'      => $variantSummary,
                'image'                => $imageUrl,
            ]
        ]);

        return redirect()->route('checkout.review');
    }
}
