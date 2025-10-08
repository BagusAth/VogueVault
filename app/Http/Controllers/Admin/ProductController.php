<?php
namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Category;
use App\Models\Product;
use Illuminate\Http\Request;

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
            'attribute_keys' => ['nullable', 'array'],
            'attribute_keys.*' => ['nullable', 'string', 'max:100'],
            'attribute_values' => ['nullable', 'array'],
            'attribute_values.*' => ['nullable', 'string', 'max:255'],
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

        $attributes = [];
        $keys = $request->input('attribute_keys', []);
        $values = $request->input('attribute_values', []);

        foreach ($keys as $index => $key) {
            $key = trim($key);
            $value = $values[$index] ?? null;

            if ($key !== '' && $value !== null && $value !== '') {
                $attributes[$key] = $value;
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
            'attributes' => !empty($attributes) ? $attributes : null,
            'is_active' => true,
        ]);

        return redirect()
            ->route('admin.products.index')
            ->with('success', 'Product berhasil ditambahkan.');
    }
}
