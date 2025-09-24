<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Customer Dashboard - VogueVault</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.1.3/dist/css/bootstrap.min.css" rel="stylesheet">
    <style>
        :root {
            --primary-color: #819A91;
            --secondary-color: #A7C1A8;
            --tertiary-color: #D1D8BE;
            --background-color: #EEEFE0;
        }
        
        body {
            background-color: var(--background-color);
        }
        
        .navbar-brand {
            color: var(--primary-color) !important;
            font-weight: bold;
        }
        
        .btn-primary {
            background-color: var(--primary-color);
            border-color: var(--primary-color);
        }
        
        .card {
            border: none;
            border-radius: 15px;
            box-shadow: 0 0 20px rgba(129, 154, 145, 0.1);
        }
        
        .product-card {
            transition: transform 0.3s ease;
        }
        
        .product-card:hover {
            transform: translateY(-5px);
        }
    </style>
</head>
<body>
    <nav class="navbar navbar-expand-lg navbar-light bg-white shadow-sm">
        <div class="container">
            <a class="navbar-brand" href="#">VogueVault</a>
            <div class="navbar-nav me-auto">
                <a class="nav-link" href="#products">Products</a>
                <a class="nav-link" href="#cart">Cart</a>
                <a class="nav-link" href="#orders">My Orders</a>
            </div>
            <div class="navbar-nav ms-auto">
                <span class="navbar-text me-3">
                    Welcome, {{ auth()->user()->name }}!
                </span>
                <form action="{{ route('logout') }}" method="POST" class="d-inline">
                    @csrf
                    <button type="submit" class="btn btn-outline-danger btn-sm">Logout</button>
                </form>
            </div>
        </div>
    </nav>

    <div class="container mt-4">
        @if(session('success'))
            <div class="alert alert-success alert-dismissible fade show" role="alert">
                {{ session('success') }}
                <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
            </div>
        @endif

        <!-- Hero Section -->
        <div class="card mb-4" style="background: linear-gradient(135deg, var(--primary-color), var(--secondary-color));">
            <div class="card-body text-white text-center py-5">
                <h1 class="display-4 fw-bold">Welcome to VogueVault</h1>
                <p class="lead">Discover the latest fashion trends and styles</p>
                <a href="#products" class="btn btn-light btn-lg">Shop Now</a>
            </div>
        </div>

        <!-- Categories Section -->
        <div class="row mb-4">
            <div class="col-md-12">
                <h2 class="mb-3">Shop by Category</h2>
            </div>
            @forelse(App\Models\Category::active()->get() as $category)
                <div class="col-md-4 mb-3">
                    <div class="card product-card text-center">
                        <div class="card-body">
                            <h5 class="card-title">{{ $category->name }}</h5>
                            <p class="card-text">{{ $category->description }}</p>
                            <a href="#" class="btn btn-primary">Browse {{ $category->name }}</a>
                        </div>
                    </div>
                </div>
            @empty
                <div class="col-md-12">
                    <div class="alert alert-info">No categories available yet.</div>
                </div>
            @endforelse
        </div>

        <!-- Featured Products Section -->
        <div class="row mb-4" id="products">
            <div class="col-md-12">
                <h2 class="mb-3">Featured Products</h2>
            </div>
            @forelse(App\Models\Product::active()->featured()->get() as $product)
                <div class="col-md-4 mb-3">
                    <div class="card product-card">
                        <div class="card-body">
                            <h5 class="card-title">{{ $product->name }}</h5>
                            <p class="card-text">{{ $product->short_description }}</p>
                            <div class="d-flex justify-content-between align-items-center">
                                <span class="h5 text-primary">${{ $product->current_price }}</span>
                                @if($product->is_on_sale)
                                    <small class="text-muted text-decoration-line-through">${{ $product->price }}</small>
                                @endif
                            </div>
                            <a href="#" class="btn btn-primary w-100 mt-2">Add to Cart</a>
                        </div>
                    </div>
                </div>
            @empty
                <div class="col-md-12">
                    <div class="alert alert-info">No featured products available yet.</div>
                </div>
            @endforelse
        </div>

        <!-- Quick Actions -->
        <div class="row">
            <div class="col-md-12">
                <div class="card">
                    <div class="card-header">
                        <h5 class="card-title mb-0">Quick Actions</h5>
                    </div>
                    <div class="card-body">
                        <div class="row">
                            <div class="col-md-3">
                                <a href="#" class="btn btn-outline-primary w-100 mb-3">
                                    <i class="bi bi-bag"></i> View Cart
                                </a>
                            </div>
                            <div class="col-md-3">
                                <a href="#" class="btn btn-outline-primary w-100 mb-3">
                                    <i class="bi bi-heart"></i> My Wishlist
                                </a>
                            </div>
                            <div class="col-md-3">
                                <a href="#" class="btn btn-outline-primary w-100 mb-3">
                                    <i class="bi bi-clock-history"></i> Order History
                                </a>
                            </div>
                            <div class="col-md-3">
                                <a href="#" class="btn btn-outline-primary w-100 mb-3">
                                    <i class="bi bi-person"></i> My Profile
                                </a>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.1.3/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>