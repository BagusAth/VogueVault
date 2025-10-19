<?php
namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Category;
use App\Models\Product;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Str;

class ProductController extends Controller
{
    /**
     * Display a listing of the products for admin dashboard.
     */
    public function index()
    {
        $products = Product::with('category')
            ->withSum('orderItems as total_sold', 'quantity')
            ->latest()
            ->get();

        return view('admin.product', [
            'products' => $products,
            'placeholderImage' => asset('images/placeholder_img.jpg'),
        ]);
    }

    /**
     * Show the form for creating a new product.
     */
    public function create()
    {
        $categories = Category::orderBy('name')->get();

        return view('admin.products.create', [
            'categories' => $categories,
            'placeholderImage' => asset('images/placeholder_img.jpg'),
        ]);
    }

    /**
     * Store a newly created product in storage.
     */
    public function store(Request $request)
    {
        $validated = $request->validate([
            'name' => ['required', 'string', 'max:255'],
            'short_description' => ['nullable', 'string', 'max:255'],
            'description' => ['nullable', 'string'],
            'price' => ['required', 'numeric', 'min:0'],
            'stock' => ['required', 'integer', 'min:0'],
            'category_id' => ['required', 'exists:categories,id'],
            'images' => ['nullable', 'array', 'max:5'],
            'images.*' => ['nullable', 'image', 'max:4096'],
            'specification_keys' => ['nullable', 'array'],
            'specification_keys.*' => ['nullable', 'string', 'max:100'],
            'specification_values' => ['nullable', 'array'],
            'specification_values.*' => ['nullable', 'string', 'max:255'],
            'variant_keys' => ['nullable', 'array'],
            'variant_keys.*' => ['nullable', 'string', 'max:100'],
            'variant_values' => ['nullable', 'array'],
            'variant_values.*' => ['nullable', 'string'],
        ]);

        $imagePaths = [];
        $imageFiles = $request->file('images', []);
        if (!is_array($imageFiles)) {
            $imageFiles = $imageFiles ? [$imageFiles] : [];
        }

        foreach ($imageFiles as $image) {
            if ($image && $image->isValid()) {
                $imagePaths[] = $image->store('products', 'public');
            }
        }

        $specifications = [];
        $specKeys = $request->input('specification_keys', []);
        $specValues = $request->input('specification_values', []);

        foreach ($specKeys as $index => $key) {
            $key = trim((string) $key);
            $value = $specValues[$index] ?? null;

            if ($key !== '' && $value !== null && $value !== '') {
                $specifications[$key] = $value;
            }
        }

        $variants = [];
        $variantKeys = $request->input('variant_keys', []);
        $variantValues = $request->input('variant_values', []);

        foreach ($variantKeys as $index => $key) {
            $key = trim((string) $key);
            $rawValue = $variantValues[$index] ?? null;

            if ($key === '' || $rawValue === null || $rawValue === '') {
                continue;
            }

            $options = [];

            if (is_array($rawValue)) {
                $options = array_values(array_filter(array_map(static function ($option) {
                    return is_string($option) ? trim($option) : null;
                }, $rawValue), static function ($option) {
                    return $option !== null && $option !== '';
                }));
            } else {
                $split = preg_split('/[,\n]+/', (string) $rawValue);
                $options = array_values(array_filter(array_map(static function ($option) {
                    return trim($option);
                }, $split ?: []), static function ($option) {
                    return $option !== '';
                }));
            }

            if (!empty($options)) {
                $variants[$key] = $options;
            }
        }

        Product::create([
            'name' => $validated['name'],
            'short_description' => $validated['short_description'] ?? null,
            'description' => $validated['description'] ?? null,
            'price' => $validated['price'],
            'stock' => $validated['stock'],
            'category_id' => $validated['category_id'],
            'images' => !empty($imagePaths) ? $imagePaths : null,
            'specifications' => !empty($specifications) ? $specifications : null,
            'variants' => !empty($variants) ? $variants : null,
            'is_active' => true,
        ]);

        return redirect()
            ->route('admin.products.index')
            ->with('success', 'Product berhasil ditambahkan.');
    }

    public function edit(Product $product)
    {
        $categories = Category::all();
        return view('admin.products.edit', compact('product', 'categories'));
    }

    public function update(Request $request, Product $product)
    {
        $validated = $request->validate([
            'name' => 'required|string|max:255',
            'price' => 'required|numeric|min:0',
            'stock' => 'required|integer|min:0',
            'category_id' => 'required|exists:categories,id',
            'images.*' => 'nullable|image|max:4096',
        ]);

        // Simpan atribut custom
        $keys = $request->input('attribute_keys', []);
        $values = $request->input('attribute_values', []);
        $attributes = [];

        foreach ($keys as $index => $key) {
            if (!empty($key) && isset($values[$index])) {
                $attributes[$key] = $values[$index];
            }
        }

        $validated['attributes'] = $attributes;

        // Update data produk
        $product->update($validated);

        // Optional: simpan gambar kalau ada yang baru
        if ($request->hasFile('images')) {
            $paths = [];
            foreach ($request->file('images') as $image) {
                $paths[] = $image->store('products', 'public');
            }
            $product->images = array_merge($product->images ?? [], $paths);
            $product->save();
        }

        return redirect()->route('admin.products.edit', $product->id)
                        ->with('success', 'Produk berhasil diperbarui!');
    }

    public function destroy(Product $product)
    {
        $images = $product->images ?? [];
        if (!is_array($images)) {
            $images = $images ? [$images] : [];
        }

        foreach ($images as $path) {
            if (Str::startsWith($path, ['http://', 'https://'])) {
                continue;
            }

            $normalized = ltrim($path, '/');

            if (Str::startsWith($normalized, 'images/')) {
                continue;
            }

            if (Str::startsWith($normalized, 'storage/')) {
                $normalized = Str::after($normalized, 'storage/');
            }

            if ($normalized === '') {
                continue;
            }

            Storage::disk('public')->delete($normalized);
        }

        $product->delete();

        return redirect()
            ->route('admin.products.index')
            ->with('success', 'Product berhasil dihapus.');
    }
}
