<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>VogueVault - Refresh Your Look</title>
    
    <!-- Bootstrap CSS -->
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <!-- Bootstrap Icons -->
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.10.0/font/bootstrap-icons.css">
    <!-- Navbar CSS -->
    <link rel="stylesheet" href="{{ asset('css/navbar.css') }}">
    <!-- Custom CSS -->
    <link rel="stylesheet" href="{{ asset('css/home.css') }}">
    
    <!-- CSRF Token -->
    <meta name="csrf-token" content="{{ csrf_token() }}">
</head>
<body>
    @include('partials.navbar')

    @php
        $productPlaceholder = asset('images/placeholder_img.jpg');
    @endphp

    <!-- Hero Section with Background -->
    <section class="hero-section">
        <div class="hero-background">
            <div class="hero-overlay">
                <div class="container">
                    <div class="row justify-content-center">
                        <div class="col-lg-10 text-center">
                            <!-- Tagline -->
                            <h1 class="hero-tagline">Refresh Your Look, Redefine Your Day.</h1>
                            
                            <!-- Search Section -->
                            <div class="search-section mt-4">
                                <form action="{{ route('search') }}" method="GET" class="search-form">
                                    <div class="search-container">
                                        <div class="search-input-group">
                                            <i class="bi bi-search search-icon"></i>
                                            <input type="text" 
                                                   class="form-control search-input" 
                                                   name="query" 
                                                   placeholder="What are you looking for?"
                                                   value="{{ request('query') }}">
                                            <button type="submit" class="search-submit-btn">
                                                <i class="bi bi-arrow-right"></i>
                                            </button>
                                        </div>
                                    </div>
                                </form>
                                
                                @if(session('search_error'))
                                    <div class="search-message alert alert-warning mt-3" role="alert">
                                        {{ session('search_error') }}
                                    </div>
                                @endif

                                <!-- Search Suggestions -->
                                <div class="search-suggestions mt-3">
                                    <button type="button" class="btn suggestion-btn" data-query="Women's Clothes">
                                        Women's Clothes
                                    </button>
                                    <button type="button" class="btn suggestion-btn" data-query="Men's Clothes">
                                        Men's Clothes
                                    </button>
                                    <button type="button" class="btn suggestion-btn" data-query="Accessories">
                                        Accessories
                                    </button>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </section>

    <!-- Main Content -->
    <main class="main-content">
        <!-- New Arrivals Section -->
        <section class="products-section py-5">
            <div class="container">
                <!-- Section Header -->
                <div class="section-header mb-4">
                    <h2 class="section-title">New Arrival</h2>
                </div>
                
                <!-- Products Carousel -->
                <div class="products-carousel-container position-relative">
                    <button class="carousel-nav-btn prev-btn" id="productsPrev" aria-label="Lihat produk sebelumnya">
                        <i class="bi bi-chevron-left"></i>
                    </button>
                    <div class="products-scroll-area">
                        <div class="products-grid">
                            @forelse($newProducts as $product)
                                @php
                                    $primaryImage = collect($product->images ?? [])->first();

                                    if ($primaryImage) {
                                        if (\Illuminate\Support\Str::startsWith($primaryImage, ['http://', 'https://'])) {
                                            // keep full URL as-is
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
                                <div class="product-item">
                                    <a href="{{ route('products.show', $product) }}" class="product-card-link text-decoration-none text-reset">
                                        <div class="product-card">
                                            <div class="product-image-container">
                                                <img src="{{ $primaryImage }}" 
                                                     alt="{{ $product->name }}" 
                                                     class="product-image"
                                                     onerror="this.onerror=null;this.src='{{ $productPlaceholder }}';">
                                            </div>
                                            <div class="product-info">
                                                <h6 class="product-name">{{ $product->name }}</h6>
                                                <p class="product-price">Rp {{ number_format($product->price, 0, ',', '.') }}</p>
                                            </div>
                                        </div>
                                    </a>
                                </div>
                            @empty
                                @for($i = 0; $i < 6; $i++)
                                    <div class="product-item">
                                        <div class="product-card">
                                            <div class="product-image-container">
                                                <div class="product-placeholder">
                                                    <i class="bi bi-image"></i>
                                                </div>
                                            </div>
                                            <div class="product-info">
                                                <h6 class="product-name">New Product</h6>
                                                <p class="product-price">Coming Soon</p>
                                            </div>
                                        </div>
                                    </div>
                                @endfor
                            @endforelse
                        </div>
                    </div>
                    
                    <!-- Navigation Arrow -->
                    <button class="carousel-nav-btn next-btn" id="productsNext" aria-label="Lihat produk berikutnya">
                        <i class="bi bi-chevron-right"></i>
                    </button>
                </div>
            </div>
        </section>

        <!-- Categories Section -->
    <section class="categories-section py-5" id="categories">
            <div class="container">
                <!-- Section Header -->
                <div class="section-header mb-4">
                    <h2 class="section-title">Category</h2>
                </div>
                
                <!-- Categories Grid -->
                <div class="categories-grid">
                    @forelse($categories as $category)
                        @php
                            $categoryImage = $category->display_image_url ?? $productPlaceholder;
                        @endphp
                        <div class="category-item">
                            <a href="{{ route('categories.show', $category) }}" class="category-link">
                                <div class="category-card">
                                    <div class="category-image-container">
                                        <img src="{{ $categoryImage }}" 
                                             alt="{{ $category->name }}" 
                                             class="category-image"
                                             onerror="this.onerror=null;this.src='{{ $productPlaceholder }}';">
                                        <div class="category-overlay">
                                            <h5 class="category-name">{{ $category->name }}</h5>
                                        </div>
                                    </div>
                                    <div class="category-meta">
                                        <span class="category-pill">
                                            {{ number_format($category->products_count ?? 0, 0, ',', '.') }} produk
                                        </span>
                                    </div>
                                </div>
                            </a>
                        </div>
                    @empty
                        <div class="category-item">
                            <div class="category-card">
                                <div class="category-image-container">
                                    <div class="category-placeholder">
                                        <i class="bi bi-tag"></i>
                                    </div>
                                    <div class="category-overlay">
                                        <h5 class="category-name">Women's Clothes</h5>
                                    </div>
                                </div>
                            </div>
                        </div>
                        <div class="category-item">
                            <div class="category-card">
                                <div class="category-image-container">
                                    <div class="category-placeholder">
                                        <i class="bi bi-tag"></i>
                                    </div>
                                    <div class="category-overlay">
                                        <h5 class="category-name">Men's Clothes</h5>
                                    </div>
                                </div>
                            </div>
                        </div>
                        <div class="category-item">
                            <div class="category-card">
                                <div class="category-image-container">
                                    <div class="category-placeholder">
                                        <i class="bi bi-tag"></i>
                                    </div>
                                    <div class="category-overlay">
                                        <h5 class="category-name">Accessories</h5>
                                    </div>
                                </div>
                            </div>
                        </div>
                    @endforelse
                </div>
            </div>
        </section>
    </main>

    <!-- Bootstrap JS -->
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
    <!-- Custom JS -->
    <script src="{{ asset('js/home.js') }}"></script>
</body>
</html>
