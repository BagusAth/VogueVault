<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>{{ $product->name }} - VogueVault</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.10.0/font/bootstrap-icons.css">
    <link rel="stylesheet" href="{{ asset('css/navbar.css') }}">
    <link rel="stylesheet" href="{{ asset('css/product.css') }}">
    <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
</head>
<body>
    @include('partials.navbar')

    @php
        $primaryImage = $images[0] ?? $placeholderImage;
        $rating = round($product->average_rating ?? 0, 1);
        $reviewCount = $product->review_count ?? 0;
        $formattedPrice = number_format($product->price, 0, ',', '.');
        $categoryName = optional($product->category)->name;

        $formatGroupLabel = function (string $key): string {
            return ucwords(str_replace(['_', '-'], ' ', $key));
        };

        // Helper function to generate variant URLs
        $generateVariantUrl = function (string $groupKey, string $optionValue) use ($activeSelections): string {
            $queryParams = request()->query();
            $newSelections = $activeSelections;
            $newSelections[$groupKey] = $optionValue;
            $queryParams['variant'] = $newSelections;
            return request()->url() . '?' . http_build_query($queryParams);
        };
    @endphp

    <div class="container product-container"
         data-base-price="{{ $product->price }}"
         data-stock="{{ $product->stock }}"
         data-placeholder="{{ $placeholderImage }}">
        <div class="row g-4">
            <div class="col-xl-4 col-lg-5">
                <nav class="breadcrumb mb-3">
                    <a href="{{ route('home') }}">Home</a>
                    @if($categoryName)
                        · <a href="#">{{ $categoryName }}</a>
                    @endif
                    · {{ $product->name }}
                </nav>

                <div class="product-gallery">
                    <img src="{{ $primaryImage }}" class="product-main-image" alt="{{ $product->name }}" id="productMainImage" onerror="this.onerror=null;this.src='{{ $placeholderImage }}';">

                    <div class="thumbnail-strip">
                        @foreach($images as $image)
                            <div class="thumbnail {{ $loop->first ? 'active' : '' }}">
                                <img src="{{ $image }}" alt="{{ $product->name }} thumbnail {{ $loop->iteration }}" onerror="this.onerror=null;this.src='{{ $placeholderImage }}';">
                            </div>
                        @endforeach
                    </div>
                </div>
            </div>

            <div class="col-xl-5 col-lg-7">
                <div class="d-flex flex-column h-100">
                    <div>
                        <h1 class="product-title">{{ $product->name }}</h1>
                        <div class="meta-row mb-3">
                            <span><i class="bi bi-box-seam"></i> Stok {{ number_format($product->stock, 0, ',', '.') }}</span>
                            @if($reviewCount > 0)
                                <span><i class="bi bi-star-fill text-warning"></i> {{ number_format($rating, 1) }} ({{ number_format($reviewCount, 0, ',', '.') }} rating)</span>
                            @else
                                <span><i class="bi bi-star"></i> Belum ada rating</span>
                            @endif
                        </div>
                        <div class="product-price mb-3">Rp {{ $formattedPrice }}</div>

                        @if(!empty($variantGroups))
                            <div class="variant-group-container mb-4">
                                <div class="info-label mb-2">Pilihan varian:</div>
                                @foreach($variantGroups as $groupKey => $options)
                                    @php
                                        $groupLabel = $formatGroupLabel($groupKey);
                                        $selectedValue = $activeSelections[$groupKey] ?? null;
                                    @endphp
                                    <div class="variant-group-block">
                                        <div class="variant-group-heading">
                                            <span class="variant-group-title">{{ $groupLabel }}</span>
                                        </div>
                                        <div class="variant-group variant-group-options" data-group="{{ $groupKey }}">
                                            @foreach($options as $option)
                                                <a href="{{ $generateVariantUrl($groupKey, $option) }}"
                                                   class="variant-option {{ $option === $selectedValue ? 'active' : '' }}"
                                                   data-group="{{ $groupKey }}"
                                                   data-value="{{ $option }}">{{ $option }}</a>
                                            @endforeach
                                        </div>
                                    </div>
                                @endforeach
                                <div class="variant-summary text-muted small">Kombinasi saat ini: <span class="variant-summary-text">{{ $variantSummary }}</span></div>
                            </div>
                        @endif
                    </div>

                    <div class="mt-auto">
                        <div class="tab-buttons btn-group" role="group">
                            <button type="button" class="btn btn-light active" data-tab="detail">Detail</button>
                            <button type="button" class="btn btn-light" data-tab="spec">Spesifikasi</button>
                            <button type="button" class="btn btn-light" data-tab="review">Review</button>
                        </div>
                        <div class="tab-pane-content mt-3" id="tab-detail">
                            <p class="mb-2"><strong>Kondisi:</strong> {{ $product->is_active ? 'Baru' : 'Nonaktif' }}</p>
                            <p class="mb-2"><strong>Min. Pemesanan:</strong> 1 Buah</p>
                            <p class="mb-2"><strong>Etalase:</strong> {{ $categoryName ?? 'Semua Etalase' }}</p>
                            <p class="mb-0 text-muted">{!! nl2br(e($product->description ?? 'Belum ada deskripsi untuk produk ini.')) !!}</p>
                        </div>
                        <div class="tab-pane-content mt-3 d-none" id="tab-spec">
                            <ul class="mb-0 text-muted">
                                @if($material)
                                    <li>Material: {{ $material }}</li>
                                @endif
                                @foreach($specifications as $key => $value)
                                    @php
                                        $label = ucwords(str_replace('_', ' ', $key));
                                        $displayValue = is_array($value) ? implode(', ', $value) : (is_bool($value) ? ($value ? 'Ya' : 'Tidak') : $value);
                                    @endphp
                                    <li>{{ $label }}: {{ $displayValue }}</li>
                                @endforeach
                                @if(!$material && empty($specifications))
                                    <li>Spesifikasi tambahan belum tersedia.</li>
                                @endif
                            </ul>
                        </div>
                        <div class="tab-pane-content mt-3 d-none" id="tab-review">
                            @if($reviewCount > 0)
                                <p class="text-muted mb-0">Produk ini memiliki {{ number_format($reviewCount, 0, ',', '.') }} ulasan dengan rata-rata rating {{ number_format($rating, 1) }} dari 5.</p>
                            @else
                                <p class="text-muted mb-0">Belum ada ulasan untuk produk ini. Jadilah yang pertama memberikan review!</p>
                            @endif
                        </div>
                    </div>
                </div>
            </div>

            <div class="col-xl-3">
                <div class="info-card position-sticky" style="top: 100px;">
                    <div class="d-flex align-items-center mb-3">
                        <img src="{{ $primaryImage }}" alt="{{ $product->name }}" class="rounded-3" style="width: 70px; height: 70px; object-fit: cover;" id="selectedImagePreview" onerror="this.onerror=null;this.src='{{ $placeholderImage }}';">
                        <div class="ms-3">
                            <div class="info-label">Pilihan varian</div>
                            <div class="variant-summary-text fw-semibold mt-1">{{ $variantSummary }}</div>
                        </div>
                    </div>

                    <div class="mb-3">
                        <div class="info-label">Atur jumlah</div>
                        <div class="qty-control mt-2">
                            <button class="qty-btn" data-action="minus">-</button>
                            <input type="text" class="qty-input" id="quantity" value="1" readonly>
                            <button class="qty-btn" data-action="plus">+</button>
                            <span class="ms-3 text-success fw-semibold stock-label">Stok: {{ number_format($product->stock, 0, ',', '.') }}</span>
                        </div>
                        <p class="variant-quantity-hint text-muted small mt-2">
                            {{ !empty($variantGroups) ? 'Jumlah untuk: ' . $variantSummary : 'Masukkan jumlah produk yang ingin kamu beli.' }}
                        </p>
                    </div>

                    <div class="d-flex justify-content-between align-items-center mb-3">
                        <span class="info-label">Subtotal</span>
                        <span class="subtotal" id="subtotal">Rp {{ $formattedPrice }}</span>
                    </div>

                    <div class="d-grid gap-2 mb-3">

                        {{-- Tambah ke Keranjang --}}
                        <form action="{{ route('cart.add', $product->id) }}" method="POST">
                            @csrf
                            <input type="hidden" name="quantity" id="cartQuantity" value="1">
                            <button type="submit" class="btn btn-cart w-100">
                                <i class="bi bi-cart-plus"></i> + Keranjang
                            </button>
                        </form>

                        {{-- Beli Sekarang --}}
                        <form action="{{ route('checkout.buyNow', $product->id) }}" method="POST">
                            @csrf
                            <input type="hidden" name="quantity" id="buyNowQuantity" value="1">
                            <button type="submit" class="btn btn-buy w-100">
                                <i class="bi bi-bag-check"></i> Beli Sekarang
                            </button>
                        </form>
                    </div>

                    <div class="inline-actions">
                        <a href="#"><i class="bi bi-chat-dots"></i> Chat</a>
                        <a href="#"><i class="bi bi-heart"></i> Wishlist</a>
                        <a href="#"><i class="bi bi-share"></i> Share</a>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
    <script src="{{ asset('js/product.js') }}"></script>

    @if(session('success'))
    <script>
    Swal.fire({
        toast: true,
        position: 'top-end',
        icon: 'success',
        title: '{{ session('success') }}',
        showConfirmButton: false,
        timer: 2000,
        timerProgressBar: true
    });
    </script>
    @endif

    <script>
    document.addEventListener("DOMContentLoaded", () => {
    const qtyInput = document.getElementById("quantity");
    const cartQty = document.getElementById("cartQuantity");
    const buyQty = document.getElementById("buyNowQuantity");

    function syncQty() {
        cartQty.value = qtyInput.value;
        buyQty.value = qtyInput.value;
    }

    document.querySelectorAll(".qty-btn").forEach(btn => {
        btn.addEventListener("click", () => {
            let val = parseInt(qtyInput.value) || 1;
            if (btn.dataset.action === "plus") val++;
            if (btn.dataset.action === "minus" && val > 1) val--;
            qtyInput.value = val;
            syncQty();
        });
    });
    syncQty();
    });
</script>
</body>
</html>
