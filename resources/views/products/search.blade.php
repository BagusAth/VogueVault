<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Hasil Pencarian "{{ $query }}" - VogueVault</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.10.0/font/bootstrap-icons.css">
    <link rel="stylesheet" href="{{ asset('css/navbar.css') }}">
    <link rel="stylesheet" href="{{ asset('css/home.css') }}">
</head>
<body>
    @include('partials.navbar')

    @php
        $productPlaceholder = asset('images/placeholder_img.jpg');
        $keywordsList = $keywords instanceof \Illuminate\Support\Collection ? $keywords : collect($keywords);
    @endphp

    <main class="main-content">
        <!-- Search Header -->
        <section class="hero-section" style="height: auto; min-height: 280px;">
            <div class="hero-background" style="height: 100%;"></div>
            <div class="hero-overlay" style="align-items: flex-start; padding-top: 60px;">
                <div class="container">
                    <div class="row justify-content-center">
                        <div class="col-xl-8 col-lg-9">
                            <h1 class="hero-tagline" style="font-size: 2.4rem;">Hasil untuk "{{ $query }}"</h1>
                            <p class="text-white-50 text-center mt-3 mb-4">
                                Menemukan {{ number_format($resultCount, 0, ',', '.') }} produk yang relevan.
                                @if($keywordsList->isNotEmpty())
                                    <span class="d-block small mt-2">Kata kunci: {{ $keywordsList->implode(', ') }}</span>
                                @endif
                            </p>

                            <form action="{{ route('search') }}" method="GET" class="search-form">
                                <div class="search-container">
                                    <div class="search-input-group">
                                        <i class="bi bi-search search-icon"></i>
                                        <input type="text"
                                               class="form-control search-input"
                                               name="query"
                                               placeholder="Cari produk lainnya..."
                                               value="{{ $query }}">
                                        <button type="submit" class="search-submit-btn">
                                            <i class="bi bi-arrow-right"></i>
                                        </button>
                                    </div>
                                </div>
                            </form>

                            @if($suggestedKeywords->isNotEmpty())
                                <div class="search-suggestions mt-3">
                                    @foreach($suggestedKeywords as $suggestion)
                                        <button type="button" class="btn suggestion-btn" data-query="{{ $suggestion }}">
                                            {{ $suggestion }}
                                        </button>
                                    @endforeach
                                </div>
                            @endif
                        </div>
                    </div>
                </div>
            </div>
        </section>

        <!-- Results Section -->
        <section class="products-section py-5">
            <div class="container">
                @if($products->count() === 0)
                    <div class="text-center py-5">
                        <div class="display-6 mb-3"><i class="bi bi-search"></i></div>
                        <h2 class="h4">Tidak ada produk yang cocok dengan pencarian Anda.</h2>
                        <p class="text-muted">Coba gunakan istilah yang berbeda atau pilih salah satu rekomendasi di atas.</p>
                    </div>
                @else
                    <div class="section-header mb-4 d-flex justify-content-between align-items-center">
                        <h2 class="section-title mb-0">Produk ditemukan</h2>
                        <span class="badge bg-light text-dark">{{ number_format($resultCount, 0, ',', '.') }} hasil</span>
                    </div>

                    <div class="row g-4">
                        @foreach($products as $product)
                            @php
                                $primaryImage = collect($product->images ?? [])->first();
                                if ($primaryImage && !\Illuminate\Support\Str::startsWith($primaryImage, ['http://', 'https://'])) {
                                    $primaryImage = asset('storage/' . ltrim($primaryImage, '/'));
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
                                            @if(optional($product->category)->name)
                                                <span class="badge bg-light text-dark">{{ $product->category->name }}</span>
                                            @endif
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
    </main>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
    <script src="{{ asset('js/home.js') }}"></script>
</body>
</html>
