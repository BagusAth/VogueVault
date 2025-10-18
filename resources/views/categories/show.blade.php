<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>{{ $category->name }} - VogueVault</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.10.0/font/bootstrap-icons.css">
    <link rel="stylesheet" href="{{ asset('css/navbar.css') }}">
    <link rel="stylesheet" href="{{ asset('css/home.css') }}">
    <link rel="stylesheet" href="{{ asset('css/category.css') }}">
</head>
<body>
    @include('partials.navbar')

    @php
        $productPlaceholder = asset('images/placeholder_img.jpg');
        $heroBackground = $category->display_image_url ?? asset('images/background.png');
    @endphp

    <main class="main-content">
        <section class="category-hero py-5" style="--category-hero-image: url('{{ $heroBackground }}');">
            <div class="container category-hero-content py-4">
                <div class="row justify-content-center">
                    <div class="col-xl-8 col-lg-9 text-center text-lg-start">
                        <h1 class="category-title">Kategori {{ $category->name }}</h1>
                        <p class="category-subtitle mb-4">
                            Menampilkan {{ number_format($resultCount, 0, ',', '.') }} produk dalam kategori ini.
                        </p>
                        <div class="d-flex flex-wrap gap-3 category-toolbar">
                            <a href="{{ route('home') }}#categories" class="btn suggestion-btn">
                                <i class="bi bi-grid"></i>
                                <span>Lihat kategori lain</span>
                            </a>
                        </div>
                    </div>
                </div>
            </div>
        </section>

        <section class="products-section py-5">
            <div class="container">
                @if($products->isEmpty())
                    <div class="text-center py-5">
                        <div class="display-6 mb-3"><i class="bi bi-box"></i></div>
                        <h2 class="h4">Belum ada produk dalam kategori ini.</h2>
                        <p class="text-muted">Silakan cek lagi nanti atau jelajahi kategori lainnya.</p>
                    </div>
                @else
                    <div class="row g-4">
                        @foreach($products as $product)
                            @php
                                $primaryImage = collect($product->images ?? [])->first();

                                if ($primaryImage) {
                                    if (\Illuminate\Support\Str::startsWith($primaryImage, ['http://', 'https://'])) {
                                        // use as-is
                                    } else {
                                        $cleanPath = ltrim($primaryImage, '/');
                                        if (\Illuminate\Support\Facades\Storage::disk('public')->exists($cleanPath)) {
                                            $primaryImage = asset('storage/' . $cleanPath);
                                        } elseif (file_exists(public_path($cleanPath))) {
                                            $primaryImage = asset($cleanPath);
                                        } else {
                                            $primaryImage = null;
                                        }
                                    }
                                }

                                $primaryImage = $primaryImage ?? $productPlaceholder;
                            @endphp
                            <div class="col-12 col-sm-6 col-lg-4 col-xl-3">
                                <a href="{{ route('products.show', $product) }}" class="product-card-link text-decoration-none text-reset">
                                    <div class="product-card h-100">
                                        <div class="product-image-container">
                                            <img src="{{ $primaryImage }}"
                                                 alt="{{ $product->name }}"
                                                 class="product-image"
                                                 onerror="this.onerror=null;this.src='{{ $productPlaceholder }}';">
                                        </div>
                                        <div class="product-info">
                                            <h6 class="product-name" title="{{ $product->name }}">{{ $product->name }}</h6>
                                            <p class="product-price">Rp {{ number_format($product->price, 0, ',', '.') }}</p>
                                        </div>
                                    </div>
                                </a>
                            </div>
                        @endforeach
                    </div>

                    <div class="d-flex justify-content-center mt-5">
                        {{ $products->links() }}
                    </div>
                @endif
            </div>
        </section>

        @if($otherCategories->isNotEmpty())
            <section class="categories-section py-5" id="categories">
                <div class="container">
                    <div class="section-header mb-4 text-center">
                        <h2 class="section-title">Kategori Lainnya</h2>
                        <p class="text-muted mt-2">Temukan inspirasi baru dengan koleksi kategori berikut.</p>
                    </div>
                    <div class="categories-grid category-suggestions">
                        @foreach($otherCategories as $otherCategory)
                            @php
                                $categoryImage = $otherCategory->display_image_url ?? $productPlaceholder;
                            @endphp
                            <div class="category-item">
                                <a href="{{ route('categories.show', $otherCategory) }}" class="category-link">
                                    <div class="category-card">
                                        <div class="category-image-container">
                                            <img src="{{ $categoryImage }}"
                                                 alt="{{ $otherCategory->name }}"
                                                 class="category-image"
                                                 onerror="this.onerror=null;this.src='{{ $productPlaceholder }}';">
                                            <div class="category-overlay">
                                                <h5 class="category-name">{{ $otherCategory->name }}</h5>
                                            </div>
                                        </div>
                                        <div class="category-meta">
                                            <span class="category-pill">
                                                {{ number_format($otherCategory->products_count ?? 0, 0, ',', '.') }} produk
                                            </span>
                                        </div>
                                    </div>
                                </a>
                            </div>
                        @endforeach
                    </div>
                </div>
            </section>
        @endif
    </main>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
    <script src="{{ asset('js/home.js') }}"></script>
    <script src="{{ asset('js/category.js') }}" defer></script>
</body>
</html>
