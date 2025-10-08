<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Admin · Products - VogueVault</title>
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.10.0/font/bootstrap-icons.css">
    <link rel="stylesheet" href="{{ asset('css/admin/product.css') }}">
@php
    use Illuminate\Support\Str;
@endphp
</head>
<body>
    <div class="layout">
        @include('admin.partials.sidebar', ['active' => 'products'])

        <main class="content">
            <div class="content-header">
                <h1 class="content-title">All Product</h1>
                <a href="{{ route('admin.products.create') }}" class="btn-add"><i class="bi bi-plus"></i> Add Product</a>
            </div>

            @if($products->isEmpty())
                <div class="product-card no-products">
                    No products found. Start adding your first product!
                </div>
            @else
                <div class="product-grid">
                    @foreach($products as $product)
                        @php
                            $imageUrl = collect($product->images ?? [])->first();
                            if ($imageUrl && !Str::startsWith($imageUrl, ['http://', 'https://'])) {
                                $imageUrl = asset('storage/' . ltrim($imageUrl, '/'));
                            }
                            $imageUrl = $imageUrl ?: $placeholderImage;

                            $description = $product->short_description ?? $product->description;
                            $description = $description ? Str::limit(strip_tags($description), 90) : 'Belum ada deskripsi.';

                            $sales = $product->total_sold ?? 0;
                        @endphp
                        <article class="product-card">
                            <header class="product-card-header">
                                <div class="product-thumb">
                                    <img src="{{ $imageUrl }}" alt="{{ $product->name }}" onerror="this.onerror=null;this.src='{{ $placeholderImage }}';">
                                </div>
                                <div class="product-meta">
                                    <span class="product-name">{{ $product->name }}</span>
                                    <span class="product-price">Rp {{ number_format($product->price, 0, ',', '.') }}</span>
                                </div>
                                <button type="button" style="border:none;background:transparent;color:var(--muted);font-size:18px;cursor:pointer;">
                                    <i class="bi bi-three-dots"></i>
                                </button>
                            </header>

                            <p class="product-description">{{ $description }}</p>

                            <div class="product-stats">
                                <span class="sales"><i class="bi bi-graph-up"></i> {{ number_format($sales, 0, ',', '.') }} sales</span>
                                <span><i class="bi bi-box-seam"></i> Stock {{ number_format($product->stock, 0, ',', '.') }}</span>
                            </div>
                        </article>
                    @endforeach
                </div>
            @endif
        </main>
    </div>
    <script src="{{ asset('js/admin/product.js') }}"></script>
</body>
</html>
