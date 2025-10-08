<?php

namespace App\Http\Controllers;

use App\Models\Product;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Str;

class ProductController extends Controller
{
    /**
     * Display the specified product.
     */
    public function show(Request $request, Product $product)
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

        $variantGroups = collect($product->variants ?? [])
            ->map(function ($options) {
                if (!is_array($options)) {
                    $options = [$options];
                }

                return collect($options)
                    ->filter(fn ($option) => $option !== null && $option !== '')
                    ->values()
                    ->all();
            })
            ->filter()
            ->all();

        // Determine active selections: URL query string > defaults
        $defaultSelections = collect($variantGroups)
            ->mapWithKeys(fn ($options, $key) => [$key => $options[0] ?? null])
            ->filter()
            ->all();

        $selectionsFromUrl = $request->query('variant', []);
        $activeSelections = array_merge($defaultSelections, $selectionsFromUrl);

        // Ensure only valid variants are kept in active selections
        foreach ($activeSelections as $key => $value) {
            if (!isset($variantGroups[$key]) || !in_array($value, $variantGroups[$key])) {
                // If invalid, revert to default for that key, or unset if no default
                $activeSelections[$key] = $defaultSelections[$key] ?? null;
                if ($activeSelections[$key] === null) {
                    unset($activeSelections[$key]);
                }
            }
        }

        $variantSummary = collect($activeSelections)
            ->map(function ($value, $key) {
                $label = ucwords(str_replace(['_', '-'], ' ', $key));
                return $label . ': ' . $value;
            })
            ->values()
            ->implode(' · ');

        if (empty($variantSummary) && !empty($variantGroups)) {
            $variantSummary = 'Pilih varian';
        } elseif (empty($variantGroups)) {
            $variantSummary = 'Default';
        }

        $specifications = collect($product->specifications ?? [])
            ->filter(fn ($value) => $value !== null && $value !== '');

        $material = $specifications->get('material');
        $specifications = $specifications->except('material');

        return view('product', [
            'product' => $product,
            'images' => $images,
            'variantGroups' => $variantGroups,
            'material' => $material,
            'specifications' => $specifications->all(),
            'activeSelections' => $activeSelections, // Use this instead of defaultSelections
            'variantSummary' => $variantSummary,
            'placeholderImage' => $placeholder,
        ]);
    }
}
