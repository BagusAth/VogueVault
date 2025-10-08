<?php

namespace App\Http\Controllers;

use App\Models\Category;
use Illuminate\Database\Eloquent\Builder;

class CategoryController extends Controller
{
    /**
     * Display products filtered by category.
     */
    public function show(Category $category)
    {
        if (!$category->is_active) {
            abort(404);
        }

        $category->loadCount(['products as products_count' => function (Builder $query) {
            $query->active();
        }]);

        $products = $category->products()
            ->active()
            ->with('category')
            ->orderBy('created_at', 'desc')
            ->paginate(12)
            ->withQueryString();

        $otherCategories = Category::active()
            ->whereKeyNot($category->getKey())
            ->withCount(['products as products_count' => function (Builder $query) {
                $query->active();
            }])
            ->with(['products' => function ($query) {
                $query->active()->select('id', 'category_id', 'images')->latest()->take(1);
            }])
            ->orderBy('name')
            ->get();

        return view('categories.show', [
            'category' => $category,
            'products' => $products,
            'otherCategories' => $otherCategories,
            'resultCount' => $products->total(),
        ]);
    }
}
