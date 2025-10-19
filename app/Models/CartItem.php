<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class CartItem extends Model
{
    protected $fillable = [
        'cart_id',
        'product_id',
        'quantity',
        'unit_price',
        'price',
        'product_attributes',
        'variant_signature',
    ];

    protected $casts = [
        'product_attributes' => 'array',
    ];

    public function cart()
    {
        return $this->belongsTo(Cart::class);
    }

    public function product()
    {
        return $this->belongsTo(Product::class);
    }

    public function getSubtotalAttribute()
    {
        return $this->quantity * $this->unit_price;
    }

    public function getVariantLabelsAttribute(): array
    {
        return collect($this->product_attributes ?? [])
            ->filter(fn ($value) => $value !== null && $value !== '')
            ->map(function ($value, $key) {
                $label = ucwords(str_replace(['_', '-'], ' ', (string) $key));
                return $label . ': ' . $value;
            })
            ->values()
            ->all();
    }
}
