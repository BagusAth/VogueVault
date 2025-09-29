<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Admin · Products - VogueVault</title>
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.10.0/font/bootstrap-icons.css">
    <style>
        :root {
            --sidebar-bg: #f0f0dc;
            --surface-bg: #f6f5eb;
            --card-bg: #ffffff;
            --accent: #5a5a4c;
            --accent-2: #3a6649;
            --muted: #7c7c72;
            --border-radius: 22px;
        }

        * {
            box-sizing: border-box;
        }

        body {
            margin: 0;
            font-family: 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif;
            background-color: var(--surface-bg);
            color: #1f1f1f;
        }

        a {
            color: inherit;
            text-decoration: none;
        }

        .layout {
            min-height: 100vh;
            display: flex;
        }

        .sidebar {
            width: 260px;
            background: radial-gradient(circle at top left, #ffffff 0%, var(--sidebar-bg) 45%, #deddc5 100%);
            padding: 32px 24px;
            display: flex;
            flex-direction: column;
            gap: 32px;
        }

        .brand {
            display: flex;
            align-items: center;
            gap: 12px;
            font-weight: 700;
            font-size: 24px;
            color: var(--accent-2);
        }

        .brand-icon {
            width: 44px;
            height: 44px;
            border-radius: 12px;
            background-color: var(--accent-2);
            color: #fff;
            display: grid;
            place-items: center;
            font-size: 20px;
        }

        .nav-links {
            display: flex;
            flex-direction: column;
            gap: 14px;
            flex: 1;
        }

        .nav-item {
            display: flex;
            align-items: center;
            gap: 12px;
            padding: 12px 16px;
            border-radius: 14px;
            color: var(--accent);
            transition: background 0.2s ease;
        }

        .nav-item.active,
        .nav-item:hover {
            background-color: rgba(58, 102, 73, 0.12);
            color: var(--accent-2);
        }

        .nav-footer {
            display: flex;
            align-items: center;
            gap: 12px;
            padding: 12px 16px;
            border-radius: 16px;
            background-color: rgba(90, 90, 76, 0.1);
        }

        .content {
            flex: 1;
            padding: 32px 48px;
            display: flex;
            flex-direction: column;
            gap: 24px;
        }

        .content-header {
            display: flex;
            justify-content: space-between;
            align-items: center;
        }

        .content-title {
            font-size: 28px;
            font-weight: 700;
            color: var(--accent);
        }

        .btn-add {
            padding: 12px 20px;
            border-radius: 999px;
            background-color: var(--accent-2);
            color: #fff;
            border: none;
            font-weight: 600;
            display: inline-flex;
            align-items: center;
            gap: 8px;
            cursor: pointer;
        }

        .product-grid {
            display: grid;
            grid-template-columns: repeat(auto-fill, minmax(240px, 1fr));
            gap: 24px;
        }

        .product-card {
            background-color: var(--card-bg);
            border-radius: 28px;
            padding: 20px;
            display: flex;
            flex-direction: column;
            gap: 14px;
            box-shadow: 0 15px 50px rgba(58, 102, 73, 0.08);
        }

        .product-card-header {
            display: flex;
            justify-content: space-between;
            align-items: flex-start;
            gap: 12px;
        }

        .product-thumb {
            width: 72px;
            height: 72px;
            border-radius: 18px;
            overflow: hidden;
            background-color: rgba(90, 90, 76, 0.1);
            flex-shrink: 0;
        }

        .product-thumb img {
            width: 100%;
            height: 100%;
            object-fit: cover;
            display: block;
        }

        .product-meta {
            flex: 1;
            display: flex;
            flex-direction: column;
            gap: 6px;
        }

        .product-name {
            font-weight: 700;
            font-size: 16px;
            color: var(--accent);
        }

        .product-price {
            font-weight: 700;
            color: var(--accent-2);
        }

        .product-description {
            font-size: 13px;
            color: var(--muted);
            line-height: 1.4;
            min-height: 3.2em;
        }

        .product-stats {
            display: flex;
            justify-content: space-between;
            font-size: 13px;
            color: var(--accent);
        }

        .product-stats span {
            display: flex;
            align-items: center;
            gap: 6px;
        }

        .product-stats .sales {
            color: var(--accent-2);
            font-weight: 600;
        }

        .no-products {
            text-align: center;
            padding: 60px 0;
            color: var(--muted);
        }

        @media (max-width: 1024px) {
            .layout {
                flex-direction: column;
            }

            .sidebar {
                width: 100%;
                flex-direction: row;
                align-items: center;
                justify-content: space-between;
                border-radius: 0 0 28px 28px;
            }

            .nav-links {
                flex-direction: row;
                flex-wrap: wrap;
                gap: 10px;
            }

            .content {
                padding: 24px;
            }
        }
    </style>
@php
    use Illuminate\Support\Str;
@endphp
</head>
<body>
    <div class="layout">
        <aside class="sidebar">
            <div class="brand">
                <div class="brand-icon">
                    <i class="bi bi-shop"></i>
                </div>
                VogueVault
            </div>

            <nav class="nav-links">
                <a href="{{ route('admin.dashboard') }}" class="nav-item">
                    <i class="bi bi-speedometer2"></i>
                    Dashboard
                </a>
                <a href="{{ route('admin.products.index') }}" class="nav-item active">
                    <i class="bi bi-bag"></i>
                    Product
                </a>
                <span class="nav-item">
                    <i class="bi bi-layout-text-window"></i>
                    Order
                </span>
                <span class="nav-item">
                    <i class="bi bi-bell"></i>
                    Notification
                </span>
                <span class="nav-item">
                    <i class="bi bi-question-circle"></i>
                    Help
                </span>
            </nav>

            <div class="nav-footer">
                <i class="bi bi-person-circle"></i>
                <div>
                    <div style="font-weight:600;">{{ auth()->user()->name ?? 'Admin' }}</div>
                    <div style="font-size:12px;color:var(--muted);">Administrator</div>
                </div>
            </div>
        </aside>

        <main class="content">
            <div class="content-header">
                <h1 class="content-title">All Product</h1>
                <a href="#" class="btn-add"><i class="bi bi-plus"></i> Add Product</a>
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
                            $description = $description ? \Illuminate\Support\Str::limit(strip_tags($description), 90) : 'Belum ada deskripsi.';

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
</body>
</html>
