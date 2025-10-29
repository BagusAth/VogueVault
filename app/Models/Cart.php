<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

use App\Models\Product;

class Cart extends Model
{
    use HasFactory;

    protected $fillable = [
        'user_id',
        'status',
    ];

    public function user()
    {
        return $this->belongsTo(User::class);
    }

    public function items()
    {
        return $this->hasMany(CartItem::class);
    }

    public function getSubtotalAttribute()
    {
        return $this->items->sum(fn ($item) => $item->subtotal);
    }

    public function addProduct(Product $product, array $attributes = [], int $quantity = 1): CartItem
    {
        $quantity = max(1, (int) $quantity);

        $availableStock = max(0, (int) $product->stock);
        if ($availableStock === 0) {
            throw new \DomainException('This product is currently unavailable.');
        }

        $normalized = collect($attributes)
            ->filter(fn ($value) => $value !== null && $value !== '')
            ->mapWithKeys(function ($value, $key) {
                $normalizedKey = is_string($key) ? trim($key) : $key;
                $normalizedValue = is_string($value) ? trim($value) : $value;

                if ($normalizedKey === '' || $normalizedValue === null || $normalizedValue === '') {
                    return [];
                }

                return [$normalizedKey => $normalizedValue];
            })
            ->sortKeys();

        $signature = $normalized->isEmpty()
            ? '__default__'
            : sha1(json_encode($normalized->all()));

        $item = $this->items()
            ->firstOrNew([
                'product_id' => $product->id,
                'variant_signature' => $signature,
            ]);

        if (!$item->exists) {
            $item->quantity = 0;
            $item->unit_price = $product->price;
        }

        $currentQuantity = (int) $item->quantity;
        $newQuantity = $currentQuantity + $quantity;

        if ($newQuantity > $availableStock) {
            $remaining = $availableStock - $currentQuantity;

            if ($remaining <= 0) {
                throw new \DomainException('The quantity in your cart already matches the available stock.');
            }

            $message = $remaining === 1
                ? 'You can only add 1 more item for this product.'
                : "You can only add up to {$remaining} more item(s) for this product.";

            throw new \DomainException($message);
        }

        $item->quantity = $newQuantity;
        $item->price = $item->unit_price * $item->quantity;
        $item->product_attributes = $normalized->all();
        $item->variant_signature = $signature;
        $item->save();

        $this->recalculateTotals();

        return $item;
    }

    public function recalculateTotals(): void
    {
        $this->loadMissing('items');

        $totalQuantity = $this->items->sum('quantity');
        $totalAmount = $this->items->sum(fn ($item) => $item->unit_price * $item->quantity);

        $this->forceFill([
            'total_items' => $totalQuantity,
            'total_amount' => $totalAmount,
        ])->save();
    }
}
