<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Admin · Dashboard - VogueVault</title>
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.10.0/font/bootstrap-icons.css">
    <link rel="stylesheet" href="{{ asset('css/admin/product.css') }}">
    <link rel="stylesheet" href="{{ asset('css/admin/dashboard.css') }}">
@php
    $userCount = \App\Models\User::count();
    $categoryCount = \App\Models\Category::count();
    $productCount = \App\Models\Product::count();
    $orderCount = \App\Models\Order::count();
@endphp
</head>
<body>
    <div class="layout">
        @include('admin.partials.sidebar', ['active' => 'dashboard'])

        <main class="content">
            <header class="topbar">
                <div class="topbar-info">
                    <span class="topbar-title">Dashboard Overview</span>
                    <span class="topbar-subtitle">Welcome back, {{ auth()->user()->name }}. Here's how we're doing today.</span>
                </div>
                <div class="topbar-actions">
                    <form action="{{ route('logout') }}" method="POST">
                        @csrf
                        <button type="submit" class="logout-button">
                            <i class="bi bi-box-arrow-right"></i> Logout
                        </button>
                    </form>
                </div>
            </header>

            @if(session('success'))
                <div class="dashboard-alert success" role="alert">
                    {{ session('success') }}
                </div>
            @endif

            <section class="stats-grid">
                <article class="stat-card">
                    <span class="stat-label">Total Users</span>
                    <span class="stat-value">{{ number_format($userCount, 0, ',', '.') }}</span>
                    <span class="stat-meta"><i class="bi bi-people"></i> Registered customers</span>
                </article>
                <article class="stat-card">
                    <span class="stat-label">Categories</span>
                    <span class="stat-value">{{ number_format($categoryCount, 0, ',', '.') }}</span>
                    <span class="stat-meta"><i class="bi bi-grid"></i> Product groupings</span>
                </article>
                <article class="stat-card">
                    <span class="stat-label">Products</span>
                    <span class="stat-value">{{ number_format($productCount, 0, ',', '.') }}</span>
                    <span class="stat-meta"><i class="bi bi-bag"></i> Active listings</span>
                </article>
                <article class="stat-card">
                    <span class="stat-label">Orders</span>
                    <span class="stat-value">{{ number_format($orderCount, 0, ',', '.') }}</span>
                    <span class="stat-meta"><i class="bi bi-receipt"></i> Awaiting fulfillment</span>
                </article>
            </section>

            <section class="quick-actions">
                <div class="quick-actions-header">
                    <h2 class="quick-actions-title">Quick Actions</h2>
                </div>
                <div class="quick-actions-body">
                    <a href="{{ route('admin.products.index') }}" class="quick-action">
                        <i class="bi bi-bag-plus"></i>
                        Manage Products
                    </a>
                    <span class="quick-action disabled">
                        <i class="bi bi-diagram-3"></i>
                        Manage Categories (Soon)
                    </span>
                    <a href="{{ route('admin.orders.index') }}" class="quick-action">
                        <i class="bi bi-truck"></i>
                        Manage Orders
                    </a>
                </div>
            </section>
        </main>
    </div>
</body>
</html>