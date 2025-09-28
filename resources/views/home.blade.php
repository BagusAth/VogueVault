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
    <!-- Custom CSS -->
    <link rel="stylesheet" href="{{ asset('css/home.css') }}">
    
    <!-- CSRF Token -->
    <meta name="csrf-token" content="{{ csrf_token() }}">
</head>
<body>
    <!-- Header -->
    <header class="vogue-header">
        <div class="container-fluid px-4">
            <div class="row align-items-center py-3">
                <!-- Logo -->
                <div class="col-auto">
                    <div class="logo-container">
                        <div class="logo-icon">
                            <i class="bi bi-building"></i>
                        </div>
                        <span class="logo-text">VogueVault</span>
                    </div>
                </div>
                
                <!-- Right Navigation -->
                <div class="col">
                    <div class="nav-icons-container">
                        <a href="#" class="nav-icon" data-bs-toggle="tooltip" title="Cart">
                            <i class="bi bi-cart3"></i>
                            <span class="nav-text">Cart</span>
                        </a>
                        <a href="#" class="nav-icon" data-bs-toggle="tooltip" title="Notifications">
                            <i class="bi bi-bell"></i>
                            <span class="nav-text">Notification</span>
                        </a>
                        <a href="#" class="nav-icon" data-bs-toggle="tooltip" title="Help">
                            <i class="bi bi-question-circle"></i>
                            <span class="nav-text">Help</span>
                        </a>
                        <!-- Profile Dropdown -->
                        <div class="dropdown d-inline-block">
                            <a class="nav-icon profile-icon dropdown-toggle text-decoration-none"
                            href="#" id="profileDropdown" role="button"
                            data-bs-toggle="dropdown" aria-expanded="false">
                                <i class="bi bi-person-circle"></i>
                            </a>

                            <ul class="dropdown-menu dropdown-menu-end" aria-labelledby="profileDropdown">
                                @auth
                                    <li class="dropdown-item text-center">
                                        <strong>{{ Auth::user()->name }}</strong>
                                    </li>
                                    <li><hr class="dropdown-divider"></li>
                                    <li>
                                        <form action="{{ route('logout') }}" method="POST">
                                            @csrf
                                            <button type="submit" class="dropdown-item text-danger">
                                                <i class="bi bi-box-arrow-right me-1"></i> Logout
                                            </button>
                                        </form>
                                    </li>
                                @endauth

                                @guest
                                    <li>
                                        <a class="dropdown-item" href="{{ route('login') }}">
                                            <i class="bi bi-box-arrow-in-right me-1"></i> Login
                                        </a>
                                    </li>
                                @endguest
                            </ul>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </header>

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
                    <div class="products-scroll-area">
                        <div class="products-grid">
                            @forelse($newProducts as $product)
                                <div class="product-item">
                                    <div class="product-card">
                                        <div class="product-image-container">
                                            @if($product->image)
                                                <img src="{{ asset('storage/' . $product->image) }}" 
                                                     alt="{{ $product->name }}" 
                                                     class="product-image">
                                            @else
                                                <div class="product-placeholder">
                                                    <i class="bi bi-image"></i>
                                                </div>
                                            @endif
                                        </div>
                                        <div class="product-info">
                                            <h6 class="product-name">{{ $product->name }}</h6>
                                            <p class="product-price">Rp {{ number_format($product->price, 0, ',', '.') }}</p>
                                        </div>
                                    </div>
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
                    <button class="carousel-nav-btn next-btn" id="productsNext">
                        <i class="bi bi-chevron-right"></i>
                    </button>
                </div>
            </div>
        </section>

        <!-- Categories Section -->
        <section class="categories-section py-5">
            <div class="container">
                <!-- Section Header -->
                <div class="section-header mb-4">
                    <h2 class="section-title">Category</h2>
                </div>
                
                <!-- Categories Grid -->
                <div class="categories-grid">
                    @forelse($categories as $category)
                        <div class="category-item">
                            <a href="/category/{{ $category->id }}" class="category-link">
                                <div class="category-card">
                                    <div class="category-image-container">
                                        @if($category->image)
                                            <img src="{{ asset('storage/' . $category->image) }}" 
                                                 alt="{{ $category->name }}" 
                                                 class="category-image">
                                        @else
                                            <div class="category-placeholder">
                                                <i class="bi bi-tag"></i>
                                            </div>
                                        @endif
                                        <div class="category-overlay">
                                            <h5 class="category-name">{{ $category->name }}</h5>
                                        </div>
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
