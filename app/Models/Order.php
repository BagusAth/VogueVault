<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Order extends Model
{
    use HasFactory;

    protected $fillable = [
        'order_number',
        'user_id',
        'subtotal',
        'grand_total',
        'total_amount',
        'status',
        'payment_method',
        'payment_status',
        'shipping_address',
        'expires_at',
    ];

    protected $casts = [
    'expires_at' => 'datetime',
    'shipping_address' => 'array',
    ];

    // Relationships
    public function user()
    {
        return $this->belongsTo(User::class);
    }

    public function cart()
    {
        return $this->belongsTo(Cart::class);
    }
}
