<?php

namespace App\Http\Controllers;

use App\Models\CartItem;
use App\Models\Product;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class CartController extends Controller
{
    public function index()
    {
        /** @var \App\Models\User $user */
        $user = Auth::user();
        $cart = $user->getCurrentCart();

    return view('cart.overview', compact('cart'));
    }

    public function add(Request $request, Product $product)
    {
        $request->validate([
            'quantity' => 'nullable|integer|min:1',
            'variants_payload' => 'nullable|string',
        ]);

        $quantity = (int) $request->input('quantity', 1);

        if ($product->stock <= 0) {
            return redirect()->back()
                ->withInput()
                ->with('error', 'This product is currently out of stock.');
        }

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
                $normalizedOptions = collect($options)->map(fn ($option) => (string) $option)->all();
                $chosen = $selectedVariants[$groupKey] ?? null;

                return !in_array($chosen, $normalizedOptions, true);
            });

            if ($missingSelection !== null) {
                return redirect()->back()
                    ->withInput()
                    ->with('error', 'Please select all variants before adding this item to your cart.');
            }
        }

        /** @var \App\Models\User $user */
        $user = Auth::user();
        $cart = $user->getCurrentCart();
        try {
            $cart->addProduct($product, $selectedVariants, $quantity);
        } catch (\DomainException $e) {
            return redirect()->back()
                ->withInput()
                ->with('error', $e->getMessage());
        }

    return redirect()->back()->with('success', 'Product added to your cart!');
    }

    public function update(Request $request, CartItem $item)
    {
        // Cek kepemilikan item
        if ($item->cart->user_id !== Auth::id()) {
            abort(403);
        }

        $validated = $request->validate([
            'quantity' => 'required|integer|min:1',
        ]);

        $newQuantity = $validated['quantity'];

        $productStock = (int) optional($item->product)->stock;

        if ($productStock <= 0) {
            return $this->handleQuantityFailure($request, $item, 'This product is no longer available. Please remove it from your cart.');
        }

        if ($newQuantity > $productStock) {
            $message = $productStock === 1
                ? 'Only 1 item left for this product.'
                : "Only {$productStock} item(s) available for this product.";

            return $this->handleQuantityFailure($request, $item, $message, $productStock);
        }

        $item->quantity = $newQuantity;
        $item->price = $item->unit_price * $item->quantity;
        $item->save();

        $item->cart->recalculateTotals();

        if ($request->expectsJson()) {
            $item->refresh();
            $cart = $item->cart()->with('items')->first();

            return response()->json([
                'success' => true,
                'item_id' => $item->id,
                'quantity' => $item->quantity,
                'item_subtotal' => $item->subtotal,
                'item_subtotal_formatted' => 'Rp ' . number_format($item->subtotal, 0, ',', '.'),
                'cart_subtotal' => $cart->subtotal,
                'cart_subtotal_formatted' => 'Rp ' . number_format($cart->subtotal, 0, ',', '.'),
                'cart_total_items' => $cart->total_items,
                'cart_total_formatted' => 'Rp ' . number_format($cart->subtotal, 0, ',', '.'),
                'available_stock' => $productStock,
            ]);
        }

    return redirect()->route('cart.overview')->with('success', 'Quantity updated.');
    }

    private function handleQuantityFailure(Request $request, CartItem $item, string $message, ?int $availableStock = null)
    {
        if ($request->expectsJson()) {
            return response()->json([
                'success' => false,
                'message' => $message,
                'item_id' => $item->id,
                'available_stock' => $availableStock,
            ], 422);
        }

        return redirect()->route('cart.overview')->with('error', $message);
    }

    public function remove(CartItem $item)
    {
        if ($item->cart->user_id !== Auth::id()) {
            abort(403);
        }

        $item->delete();
        $item->cart->recalculateTotals();

    return redirect()->route('cart.overview')->with('success', 'Product removed from the cart.');
    }

    public function clear()
    {
        /** @var \App\Models\User $user */
        $user = Auth::user();
        $cart = $user->getCurrentCart();
        $cart->items()->delete();
        $cart->recalculateTotals();

    return redirect()->route('cart.overview')->with('success', 'Cart cleared.');
    }
    
}