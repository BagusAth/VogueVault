<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class OrderItem extends Model
{
    protected $fillable = [
        'order_id',
        'product_id',
        'product_name',
        'quantity',
        'unit_price',
        'subtotal',
        'product_price',
        'total_price',
        'selected_attributes',
    ];

    protected $casts = [
        'selected_attributes' => 'array',
    ];

    // Relasi ke Order
    public function order()
    {
        return $this->belongsTo(Order::class);
    }

    // Relasi ke Product
    public function product()
    {
        return $this->belongsTo(Product::class);
    }

    public function getVariantLabelsAttribute(): array
    {
        return collect($this->selected_attributes ?? [])
            ->filter(fn ($value) => $value !== null && $value !== '')
            ->map(function ($value, $key) {
                $label = ucwords(str_replace(['_', '-'], ' ', (string) $key));
                return $label . ': ' . $value;
            })
            ->values()
            ->all();
    }
}
