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
                <nav class="product-breadcrumb mb-3" aria-label="Breadcrumb">
                    <ol class="breadcrumb-list">
                        <li class="breadcrumb-item"><a href="{{ route('home') }}">Home</a></li>
                        @if($categoryName)
                            <li class="breadcrumb-item"><a href="#">{{ $categoryName }}</a></li>
                        @endif
                        <li class="breadcrumb-item active" aria-current="page">{{ $product->name }}</li>
                    </ol>
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
                            <span class="meta-stock"><i class="bi bi-box-seam"></i> Stock {{ number_format($product->stock, 0, ',', '.') }}</span>
                        </div>
                        <div class="product-price mb-3">Rp {{ $formattedPrice }}</div>

                        @if(!empty($variantGroups))
                            <div class="variant-group-container mb-4">
                                <div class="info-label mb-2">Variant options:</div>
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
                                <div class="variant-summary text-muted small">Current combination: <span class="variant-summary-text">{{ $variantSummary }}</span></div>
                            </div>
                        @endif
                    </div>

                    <div class="mt-auto">
                        <div class="tab-buttons" role="tablist">
                            <button type="button" class="tab-toggle active" data-tab="detail">Details</button>
                            <button type="button" class="tab-toggle" data-tab="spec">Specifications</button>
                        </div>
                        <div class="tab-pane-content mt-2" id="tab-detail" data-tab-pane="detail">
                            <p class="mb-2"><strong>Condition:</strong> {{ $product->is_active ? 'New' : 'Inactive' }}</p>
                            <p class="mb-2"><strong>Minimum Order:</strong> 1 item</p>
                            <p class="mb-2"><strong>Category:</strong> {{ $categoryName ?? 'All Categories' }}</p>
                            <p class="mb-0 text-muted">{!! nl2br(e($product->description ?? 'No description available for this product yet.')) !!}</p>
                        </div>
                        <div class="tab-pane-content mt-2 d-none" id="tab-spec" data-tab-pane="spec" hidden>
                            <ul class="mb-0 text-muted">
                                @if($material)
                                    <li>Material: {{ $material }}</li>
                                @endif
                                @foreach($specifications as $key => $value)
                                    @php
                                        $label = ucwords(str_replace('_', ' ', $key));
                                        $displayValue = is_array($value) ? implode(', ', $value) : (is_bool($value) ? ($value ? 'Yes' : 'No') : $value);
                                    @endphp
                                    <li>{{ $label }}: {{ $displayValue }}</li>
                                @endforeach
                                @if(!$material && empty($specifications))
                                    <li>Additional specifications are not available yet.</li>
                                @endif
                            </ul>
                        </div>
                    </div>
                </div>
            </div>

            <div class="col-xl-3">
                <div class="info-card position-sticky" style="top: 100px;">
                    <div class="d-flex align-items-center mb-4">
                        <img src="{{ $primaryImage }}" alt="{{ $product->name }}" class="rounded-3" style="width: 70px; height: 70px; object-fit: cover;" id="selectedImagePreview" onerror="this.onerror=null;this.src='{{ $placeholderImage }}';">
                        <div class="ms-3">
                            <div class="info-label">Variant selection</div>
                            <div class="variant-summary-text fw-semibold mt-1">{{ $variantSummary }}</div>
                        </div>
                    </div>

                    <div class="mb-4">
                        <div class="info-label">Set quantity</div>
                        <div class="qty-control mt-2">
                            <button class="qty-btn" data-action="minus">-</button>
                            <input type="text" class="qty-input" id="quantity" value="1" readonly>
                            <button class="qty-btn" data-action="plus">+</button>
                            <span class="ms-3 text-success fw-semibold stock-label">Stock: {{ number_format($product->stock, 0, ',', '.') }}</span>
                        </div>
                        @if(empty($variantGroups))
                            <p class="variant-quantity-hint text-muted small mt-2">
                                Enter the quantity you would like to purchase.
                            </p>
                        @endif
                    </div>

                    <div class="d-flex justify-content-between align-items-center mb-4">
                        <span class="info-label">Subtotal</span>
                        <span class="subtotal" id="subtotal">Rp {{ $formattedPrice }}</span>
                    </div>

                    <div class="d-grid gap-3">
                        {{-- Add to Cart --}}
                        <form action="{{ route('cart.add', $product->id) }}" method="POST" class="d-grid">
                            @csrf
                            <input type="hidden" name="quantity" id="cartQuantity" value="1">
                            <input type="hidden" name="variants_payload" id="cartVariantsPayload" value='@json($activeSelections)'>
                            <button type="submit" class="btn-cart">
                                <i class="bi bi-cart-plus"></i> Add to Cart
                            </button>
                        </form>

                        {{-- Buy Now --}}
                        <form action="{{ route('checkout.buyNow', $product->id) }}" method="POST" class="d-grid">
                            @csrf
                            <input type="hidden" name="quantity" id="buyNowQuantity" value="1">
                            <input type="hidden" name="variants_payload" id="buyNowVariantsPayload" value='@json($activeSelections)'>
                            <button type="submit" class="btn-buy">
                                <i class="bi bi-bag-check"></i> Buy Now
                            </button>
                        </form>
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

</body>
</html>
