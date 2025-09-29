<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Product;
use App\Models\Category;
use Carbon\Carbon;
use Illuminate\Support\Collection;
use Illuminate\Database\Eloquent\Builder;

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
        $query = trim((string) $request->get('query', ''));

        if ($query === '') {
            return redirect()
                ->route('home')
                ->with('search_error', 'Silakan masukkan kata kunci untuk mencari produk.');
        }

        $keywords = Collection::make(preg_split('/\s+/', $query))
            ->filter(fn ($keyword) => $keyword !== '' && mb_strlen($keyword) > 1)
            ->map(fn ($keyword) => trim($keyword))
            ->values();

        $productsQuery = Product::query()
            ->active()
            ->with('category');

        $productsQuery->where(function (Builder $matchingQuery) use ($query, $keywords) {
            $matchingQuery->where(function (Builder $phraseMatch) use ($query) {
                $this->applySearchTerm($phraseMatch, $query);
            });

            if ($keywords->count() > 1) {
                $matchingQuery->orWhere(function (Builder $allKeywordsMatch) use ($keywords) {
                    foreach ($keywords as $keyword) {
                        $allKeywordsMatch->where(function (Builder $singleKeywordMatch) use ($keyword) {
                            $this->applySearchTerm($singleKeywordMatch, $keyword);
                        });
                    }
                });
            }
        });

        $products = $productsQuery
            ->orderBy('name')
            ->paginate(12)
            ->withQueryString();

        $suggestedKeywords = $products->getCollection()
            ->filter(fn (Product $product) => optional($product->category)->name)
            ->map(fn (Product $product) => $product->category->name)
            ->unique()
            ->values();

        return view('products.search', [
            'products' => $products,
            'query' => $query,
            'keywords' => $keywords,
            'resultCount' => $products->total(),
            'suggestedKeywords' => $suggestedKeywords,
        ]);
    }

    /**
     * Apply a search term across product fields and category relation.
     */
    private function applySearchTerm(Builder $builder, string $term): void
    {
        $likeTerm = '%' . $term . '%';

        $builder->where(function (Builder $searchable) use ($likeTerm) {
            $searchable->where('name', 'LIKE', $likeTerm)
                ->orWhere('short_description', 'LIKE', $likeTerm)
                ->orWhere('description', 'LIKE', $likeTerm)
                ->orWhereHas('category', function (Builder $categoryQuery) use ($likeTerm) {
                    $categoryQuery->where('name', 'LIKE', $likeTerm);
                });
        });
    }
}