<?php

namespace App\Http\Controllers;

use App\Models\Order;
use App\Models\OrderItem;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Auth;
use App\Models\Product;


class CheckoutController extends Controller
{
    public function index()
    {
        /** @var \App\Models\User $user */
        $user = Auth::user();
        $cart = $user->getCurrentCart();

        if (session()->has('buy_now')) {
        $buyNow = session('buy_now');
        $address = session('checkout_address', [
            'receiver_name' => $user->receiver_name,
            'phone'         => $user->phone,
            'address_line'  => $user->address,
            'city'          => $user->city,
            'postal_code'   => $user->postal_code,
        ]);
        
        return view('Checkout.index', [
            'buyNow' => $buyNow,
            'cart'   => null,
            'address' => $address
        ]);
    }

        if (!$cart || $cart->items->isEmpty()) {
            return redirect()->route('cart.index')->with('error', 'Keranjang kosong!');
        }

        $address = session('checkout_address', [
            'receiver_name' => $user->receiver_name,
            'phone'         => $user->phone,
            'address_line'  => $user->address,
            'city'          => $user->city,
            'postal_code'   => $user->postal_code,
    ]);
        return view('Checkout.index', compact('cart', 'address'));

    }

    public function saveAddress(Request $request)
    {
        $request->validate([
            'receiver_name' => 'required|string|max:100',
            'phone' => 'required|string|max:20',
            'address_line' => 'required|string|max:255',
            'city' => 'required|string|max:100',
            'postal_code' => 'required|string|max:10',
        ]);

        session([
        'checkout_address' => [
            'receiver_name' => $request->receiver_name,
            'phone'         => $request->phone,
            'address_line'  => $request->address_line,
            'city'          => $request->city,
            'postal_code'   => $request->postal_code,
        ]
    ]);

        /** @var \App\Models\User $user */
        $user = Auth::user();
        $user->update([
        'receiver_name' => $request->receiver_name,
        'phone' => $request->phone,
        'address' => $request->address_line,
        'city' => $request->city,
        'postal_code' => $request->postal_code,
        ]);

        return redirect()->route('checkout.index')->with('success', 'Alamat berhasil disimpan!');
    }

    public function store(Request $request)
    {
        $request->validate([
            'payment_method' => 'required|in:gopay,shopeepay,qris,va',
        ]);

        $address = session('checkout_address');
        if (!$address || empty($address['address_line'])) {
            return redirect()->route('checkout.index')
                ->with('error', 'Isi alamat pengiriman terlebih dahulu!');
        }

        /** @var \App\Models\User $user */
        $user = Auth::user();

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
                'shipping_address' => $user->address,
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
            ]);

            // kurangi stok
            Product::find($buyNow['product_id'])->decrement('stock', $buyNow['quantity']);

            // hapus session biar ga bentrok
            session()->forget('buy_now');

            return redirect()->route('checkout.payment', ['order' => $order->id]);
        }

        $cart = $user->getCurrentCart();
        if (!$cart || $cart->items->isEmpty()) {
            return redirect()->route('cart.index')->with('error', 'Keranjang kosong!');
        }

        $order = DB::transaction(function () use ($user, $cart, $request) {
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
                'shipping_address' => $user->address,
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

        return view('checkout.payment', compact('order'));
    }

    public function buyNow(Request $request, Product $product)
    {
        $user = Auth::user();

       $quantity = $request->input('quantity', 1);

        session([
            'buy_now' => [
                'product_id'   => $product->id,
                'product_name' => $product->name,
                'price'        => $product->price,
                'color'        => $request->input('color', null),
                'quantity'     => $quantity,
                'subtotal'     => $product->price * $quantity,
                'image'        => is_array($product->images) 
                                    ? $product->images[0] 
                                    : json_decode($product->images, true)[0] ?? 'https://via.placeholder.com/80',
            ]
        ]);

        return redirect()->route('checkout.index');
    }
}    
