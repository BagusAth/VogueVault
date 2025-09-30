<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

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

    public function addProduct(Product $product, int $quantity = 1): CartItem
    {
        $item = $this->items()->firstOrNew([
            'product_id' => $product->id,
        ]);

        if (!$item->exists) {
            $item->unit_price = $product->price;
        }

        $item->quantity = ($item->quantity ?? 0) + $quantity;
        $item->save();

        return $item;
    }
}
