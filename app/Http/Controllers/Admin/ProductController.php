<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Product;

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

        return view('admin.products.index', [
            'products' => $products,
            'placeholderImage' => asset('images/placeholder_img.jpg'),
        ]);
    }
}
