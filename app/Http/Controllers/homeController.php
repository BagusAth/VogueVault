<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Product;
use App\Models\Category;
use Carbon\Carbon;

class HomeController extends Controller
{
    /**
     * Display the customer homepage with new products and categories
     *
     * @return \Illuminate\Contracts\Support\Renderable
     */
    public function index()
    {
        // Get new products from the last 7 days
        $newProducts = Product::where('created_at', '>=', Carbon::now()->subDays(7))
                              ->orderBy('created_at', 'desc')
                              ->limit(6)
                              ->get();
        
        // Get all active categories
        $categories = Category::where('is_active', true)
                             ->orderBy('name', 'asc')
                             ->get();
        
        return view('home', compact('newProducts', 'categories'));
    }

    /**
     * Handle search functionality
     */
    public function search(Request $request)
    {
        $query = $request->get('query');
        
        if (empty($query)) {
            return redirect()->route('home');
        }
        
        $products = Product::where('name', 'LIKE', '%' . $query . '%')
                          ->orWhere('description', 'LIKE', '%' . $query . '%')
                          ->paginate(12);
        
        return view('products.search', compact('products', 'query'));
    }
}