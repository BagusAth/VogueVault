<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Shopping Cart</title>
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.10.0/font/bootstrap-icons.css">
    <link rel="stylesheet" href="{{ asset('css/navbar.css') }}">
    <link rel="stylesheet" href="{{ asset('css/cart.css') }}">
</head>
<body>
    @include('partials.navbar')

    @php
        $placeholderImage = asset('images/placeholder_img.jpg');
    $itemCount = $cart->items->count();
    $itemLabel = $itemCount === 1 ? 'item' : 'items';
    @endphp

    <main class="cart-page">


        <section class="cart-content py-5">
            <div class="container">
                @if($cart->items->isEmpty())
                    <section class="cart-empty">
                        <div class="cart-empty__hero">
                            <h1 class="cart-empty__title">Shopping Cart</h1>
                            <p class="cart-empty__subtitle">Gather your favorite pieces and continue to checkout whenever you’re ready.</p>
                        </div>

                        <div class="cart-empty__panel">
                            <div class="cart-empty__panel-inner">
                                <div class="cart-empty__icon" aria-hidden="true">
                                    <i class="bi bi-inboxes"></i>
                                </div>
                                <p class="cart-empty__message">Your cart is currently empty.</p>
                                <a href="{{ route('home') }}" class="btn btn-primary cart-empty__cta">
                                    <i class="bi bi-compass"></i> Browse products
                                </a>
                            </div>
                        </div>
                    </section>
                @else
                    <div class="row g-4">
                        <div class="col-lg-8">
                            <div class="cart-items-stack">
                                @foreach($cart->items as $item)
                                    @php
                                        $rawImage = collect($item->product->images ?? [])->first();
                                        if ($rawImage) {
                                            if (\Illuminate\Support\Str::startsWith($rawImage, ['http://', 'https://'])) {
                                                $imageUrl = $rawImage;
                                            } else {
                                                $cleanPath = ltrim($rawImage, '/');
                                                if (\Illuminate\Support\Facades\Storage::disk('public')->exists($cleanPath)) {
                                                    $imageUrl = asset('storage/' . $cleanPath);
                                                } elseif (file_exists(public_path($cleanPath))) {
                                                    $imageUrl = asset($cleanPath);
                                                } else {
                                                    $imageUrl = null;
                                                }
                                            }
                                        } else {
                                            $imageUrl = null;
                                        }

                                        $imageUrl = $imageUrl ?? $placeholderImage;
                                        $variantLabels = collect($item->variant_labels ?? []);
                                    @endphp

                                    <article class="cart-item-card">
                                        <div class="cart-item-thumb">
                                            <img src="{{ $imageUrl }}" alt="{{ $item->product->name }}" onerror="this.onerror=null;this.src='{{ $placeholderImage }}';">
                                        </div>
                                        <div class="cart-item-body">
                                            <div class="cart-item-header">
                                                <div>
                                                    <h3 class="item-name">{{ $item->product->name }}</h3>
                                                    <p class="item-sku">Rp {{ number_format($item->unit_price, 0, ',', '.') }}</p>
                                                </div>
                                                <form action="{{ route('cart.remove', $item) }}" method="POST" class="remove-form">
                                                    @csrf
                                                    @method('DELETE')
                                                    <button type="submit" class="btn btn-link text-danger">
                                                        <i class="bi bi-trash"></i> Remove
                                                    </button>
                                                </form>
                                            </div>

                                            @if($variantLabels->isNotEmpty())
                                                <div class="item-variants">
                                                    @foreach($variantLabels as $variant)
                                                        <span class="variant-pill">{{ $variant }}</span>
                                                    @endforeach
                                                </div>
                                            @endif

                                            <div class="cart-item-footer">
                                                <form action="{{ route('cart.update', $item) }}" method="POST" class="quantity-form" data-item-id="{{ $item->id }}">
                                                    @csrf
                                                    <div class="input-group quantity-group">
                                                        <button type="button" class="btn btn-outline-secondary qty-btn" data-action="decrement">−</button>
                                                        <input type="number" name="quantity" value="{{ $item->quantity }}" min="1" class="form-control form-control-sm qty-input" data-quantity-input>
                                                        <button type="button" class="btn btn-outline-secondary qty-btn" data-action="increment">+</button>
                                                    </div>
                                                </form>
                                                <div class="item-subtotal">
                                                    <span class="label">Subtotal</span>
                                                    <span class="value" data-item-subtotal="{{ $item->id }}">Rp {{ number_format($item->subtotal, 0, ',', '.') }}</span>
                                                </div>
                                            </div>
                                        </div>
                                    </article>
                                @endforeach
                            </div>
                        </div>
                        <div class="col-lg-4">
                            <aside class="cart-summary-card">
                                <div class="summary-header">
                                    <h2>Summary</h2>
                                    <p>We will double-check your details during checkout.</p>
                                </div>

                                <dl class="summary-list">
                                    <div class="summary-row">
                                        <dt>Subtotal</dt>
                                        <dd data-cart-subtotal>Rp {{ number_format($cart->subtotal, 0, ',', '.') }}</dd>
                                    </div>
                                    <div class="summary-row">
                                        <dt>Estimated tax</dt>
                                        <dd class="text-muted">Calculated at checkout</dd>
                                    </div>
                                    <div class="summary-row">
                                        <dt>Shipping</dt>
                                        <dd class="text-success">Free</dd>
                                    </div>
                                </dl>

                                <div class="summary-total">
                                    <span>Total</span>
                                    <strong data-cart-total>Rp {{ number_format($cart->subtotal, 0, ',', '.') }}</strong>
                                </div>

                                <div class="summary-actions">
                                    <form action="{{ route('cart.clear') }}" method="POST">
                                        @csrf
                                        @method('DELETE')
                                        <button type="submit" class="btn btn-outline-secondary w-100">
                                            <i class="bi bi-x-circle"></i> Clear cart
                                        </button>
                                    </form>
                                    <a href="{{ route('checkout.review') }}" class="btn btn-primary w-100">
                                        Proceed to checkout
                                    </a>
                                    <a href="{{ route('home') }}" class="btn btn-link w-100">
                                        <i class="bi bi-compass"></i> Find more products
                                    </a>
                                </div>
                            </aside>
                        </div>
                    </div>
                @endif
            </div>
        </section>
    </main>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
    <script>
        (function () {
            const csrfToken = document.querySelector('meta[name="csrf-token"]').getAttribute('content');
            const summarySubtotal = document.querySelector('[data-cart-subtotal]');
            const summaryTotal = document.querySelector('[data-cart-total]');

            const sendUpdateRequest = async (form, quantity) => {
                const url = form.getAttribute('action');

                form.dataset.updating = 'true';
                form.classList.add('is-updating');

                try {
                    const response = await fetch(url, {
                        method: 'POST',
                        headers: {
                            'Content-Type': 'application/json',
                            'Accept': 'application/json',
                            'X-CSRF-TOKEN': csrfToken,
                            'X-Requested-With': 'XMLHttpRequest',
                        },
                        body: JSON.stringify({ quantity }),
                    });

                    if (!response.ok) {
                        throw new Error('Request failed');
                    }

                    const data = await response.json();
                    if (!data.success) {
                        throw new Error('Failed to update the cart');
                    }

                    const input = form.querySelector('[data-quantity-input]');
                    if (input) {
                        input.value = data.quantity;
                    }

                    const itemSubtotalTarget = document.querySelector(`[data-item-subtotal="${data.item_id}"]`);
                    if (itemSubtotalTarget) {
                        itemSubtotalTarget.textContent = data.item_subtotal_formatted;
                    }

                    if (summarySubtotal) {
                        summarySubtotal.textContent = data.cart_subtotal_formatted;
                    }

                    if (summaryTotal) {
                        summaryTotal.textContent = data.cart_total_formatted;
                    }
                } catch (error) {
                    console.error(error);
                    alert('Unable to update the product quantity. Please try again.');
                } finally {
                    form.dataset.updating = 'false';
                    form.classList.remove('is-updating');

                    if (form.dataset.pendingQuantity) {
                        const pending = parseInt(form.dataset.pendingQuantity, 10);
                        delete form.dataset.pendingQuantity;
                        if (!Number.isNaN(pending)) {
                            sendUpdateRequest(form, pending);
                        }
                    }
                }
            };

            document.querySelectorAll('.quantity-form').forEach(form => {
                const input = form.querySelector('[data-quantity-input]');
                const buttons = form.querySelectorAll('.qty-btn');

                const dispatchUpdate = (newQuantity) => {
                    const quantity = Math.max(1, parseInt(newQuantity || '1', 10));
                    if (String(quantity) !== input.value) {
                        input.value = quantity;
                    }

                    if (form.dataset.updating === 'true') {
                        form.dataset.pendingQuantity = quantity;
                    } else {
                        sendUpdateRequest(form, quantity);
                    }
                };

                buttons.forEach(button => {
                    button.addEventListener('click', () => {
                        let current = parseInt(input.value || '1', 10);
                        if (button.dataset.action === 'decrement') {
                            current = Math.max(1, current - 1);
                        } else {
                            current += 1;
                        }
                        input.value = current;
                        dispatchUpdate(current);
                    });
                });

                input.addEventListener('change', () => {
                    dispatchUpdate(input.value);
                });

                input.addEventListener('input', () => {
                    // Prevent negative values during typing
                    if (parseInt(input.value, 10) < 1) {
                        input.value = '1';
                    }
                });
            });
        })();
    </script>
</body>
</html>
