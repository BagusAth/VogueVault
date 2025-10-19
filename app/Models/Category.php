<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Collection;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Str;

class Category extends Model
{
    use HasFactory;

    protected ?string $cachedDisplayImageUrl = null;

    protected $fillable = [
        'name',
        'description',
        'image',
        'is_active',
    ];

    protected $casts = [
        'is_active' => 'boolean',
    ];

    public function products()
    {
        return $this->hasMany(Product::class);
    }

    public function scopeActive($query)
    {
        return $query->where('is_active', true);
    }

    public function getDisplayImageUrlAttribute(): string
    {
        if ($this->cachedDisplayImageUrl !== null) {
            return $this->cachedDisplayImageUrl;
        }

        if ($resolved = $this->resolveMediaUrl($this->image)) {
            return $this->cachedDisplayImageUrl = $resolved;
        }

        $productSource = $this->relationLoaded('products')
            ? $this->products
            : $this->products()->active()->latest()->take(1)->get();

        $firstProduct = $productSource instanceof Collection
            ? $productSource->first()
            : null;

        if ($firstProduct) {
            $firstImage = collect($firstProduct->images ?? [])->first();

            if ($resolved = $this->resolveMediaUrl($firstImage)) {
                return $this->cachedDisplayImageUrl = $resolved;
            }
        }

        return $this->cachedDisplayImageUrl = asset('images/placeholder_img.jpg');
    }

    protected function resolveMediaUrl(?string $path): ?string
    {
        if (!$path) {
            return null;
        }

        if (Str::startsWith($path, ['http://', 'https://'])) {
            return $path;
        }
        $cleanPath = ltrim($path, '/');

        if (Storage::disk('public')->exists($cleanPath)) {
            return route('media.show', ['path' => $cleanPath]);
        }

        if (file_exists(public_path($cleanPath))) {
            return asset($cleanPath);
        }

        return null;
    }
}
