<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class UserAddress extends Model
{
    use HasFactory;

    protected $fillable = [
        'user_id',
        'label',
        'receiver_name',
        'phone',
        'address_line',
        'city',
        'postal_code',
        'is_default',
    ];

    protected $casts = [
        'is_default' => 'boolean',
    ];

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    public function toShippingArray(): array
    {
        return [
            'receiver_name' => $this->receiver_name,
            'phone' => $this->phone,
            'address_line' => $this->address_line,
            'city' => $this->city,
            'postal_code' => $this->postal_code,
        ];
    }
}
