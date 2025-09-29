<?php

namespace App\Http\Controllers;

use App\Models\Product;
use Illuminate\Support\Arr;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Str;

class ProductController extends Controller
{
    /**
     * Display the specified product.
     */
    public function show(Product $product)
    {
    $placeholder = asset('images/placeholder_img.jpg');

        $images = collect($product->images ?? [])
            ->map(function ($path) {
                if (empty($path)) {
                    return null;
                }

                if (Str::startsWith($path, ['http://', 'https://'])) {
                    return $path;
                }

                $cleanPath = ltrim($path, '/');
                if (Storage::disk('public')->exists($cleanPath)) {
                    return asset('storage/' . $cleanPath);
                }

                return null;
            })
            ->filter()
            ->values()
            ->all();

        if (empty($images)) {
            $images[] = $placeholder;
        }

        $attributes = $product->attributes ?? [];

        $variantOptions = collect($attributes['colors'] ?? $attributes['sizes'] ?? [])
            ->filter()
            ->values()
            ->all();

        if (empty($variantOptions)) {
            $variantOptions = ['Default'];
        }

        $material = $attributes['material'] ?? null;

        $extraAttributes = Arr::except($attributes, ['colors', 'sizes', 'material']);

        return view('product', [
            'product' => $product,
            'images' => $images,
            'variantOptions' => $variantOptions,
            'material' => $material,
            'extraAttributes' => $extraAttributes,
            'defaultVariant' => $variantOptions[0] ?? 'Default',
            'placeholderImage' => $placeholder,
        ]);
    }
}
