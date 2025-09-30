<?php

namespace App\Http\Controllers;

use App\Models\Order;
use App\Models\OrderItem;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Auth;

class CheckoutController extends Controller
{
    public function index()
    {
        /** @var \App\Models\User $user */
        $user = Auth::user();
        $cart = $user->getCurrentCart();

        if (!$cart || $cart->items->isEmpty()) {
            return redirect()->route('cart.index')->with('error', 'Keranjang kosong!');
        }

        return view('checkout.index', compact('cart'));
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

        /** @var \App\Models\User $user */
        $user = Auth::user();
        $user->update([
            'name' => $request->receiver_name,
            'phone' => $request->phone,
            'address' => $request->address_line . ', ' . $request->city . ' ' . $request->postal_code,
        ]);

        return redirect()->route('checkout.index')->with('success', 'Alamat disimpan!');
    }

    public function store(Request $request)
    {
        $request->validate([
            'payment_method' => 'required|in:gopay,shopeepay,qris,va',
        ]);

        /** @var \App\Models\User $user */
        $user = Auth::user();
        $cart = $user->getCurrentCart();

        if (!$cart || $cart->items->isEmpty()) {
            return redirect()->route('cart.index')->with('error', 'Keranjang kosong!');
        }

        $order = DB::transaction(function () use ($user, $cart, $request) {
            $orderNumber = 'VV-' . now()->format('ymdHis') . '-U' . $user->id;

            $order = Order::create([
                'order_number' => $orderNumber,
                'user_id' => $user->id,
                'subtotal' => $cart->subtotal,
                'grand_total' => $cart->subtotal,
                'status' => 'pending',
                'payment_method' => $request->payment_method,
                'payment_status' => 'unpaid',
                'shipping_address' => $user->address,
                'expires_at' => now()->addHour(),
            ]);

            foreach ($cart->items as $item) {
                OrderItem::create([
                    'order_id' => $order->id,
                    'product_id' => $item->product_id,
                    'product_name' => $item->product->name,
                    'quantity' => $item->quantity,
                    'unit_price' => $item->unit_price,
                    'subtotal' => $item->subtotal,
                ]);

                $item->product->decrement('stock', $item->quantity);
            }

            $cart->update(['status' => 'checked_out']);

            return $order;
        });

        return redirect()->route('checkout.payment', $order);
    }

    public function payment(Order $order)
    {
        if ($order->user_id !== Auth::id()) {
            abort(403);
        }

        return view('checkout.payment', compact('order'));
    }
}