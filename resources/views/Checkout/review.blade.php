<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Checkout - VogueVault</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.10.0/font/bootstrap-icons.css">
    <link rel="stylesheet" href="{{ asset('css/navbar.css') }}">
    <link rel="stylesheet" href="{{ asset('css/checkout.css') }}">
</head>
<body>
    @include('partials.navbar')

    @php
        $placeholderImage = asset('images/placeholder_img.jpg');

        $formatCurrency = fn ($value) => 'Rp' . number_format($value, 0, ',', '.');

        $resolveProductImage = function ($product) use ($placeholderImage) {
            $rawImage = collect($product->images ?? [])->first();
            if (!$rawImage) {
                return $placeholderImage;
            }

            if (\Illuminate\Support\Str::startsWith($rawImage, ['http://', 'https://'])) {
                return $rawImage;
            }

            $cleanPath = ltrim($rawImage, '/');
            if (\Illuminate\Support\Facades\Storage::disk('public')->exists($cleanPath)) {
                return asset('storage/' . $cleanPath);
            }

            if (file_exists(public_path($cleanPath))) {
                return asset($cleanPath);
            }

            return $placeholderImage;
        };
    @endphp

    <main class="checkout-page py-5">
        <div class="container-xl">
            <div class="checkout-intro">
                <div>
                    <p class="eyebrow">Langkah Terakhir</p>
                    <h1>Periksa &amp; Konfirmasi Pesanan</h1>
                    <p class="lead">Pilih alamat favoritmu, pastikan detail pesanan sudah sesuai, lalu pilih metode pembayaran yang paling nyaman.</p>
                </div>
            </div>

            @if(session('error'))
                <div class="alert alert-danger shadow-sm">{{ session('error') }}</div>
            @endif
            @if(session('success'))
                <div class="alert alert-success shadow-sm">{{ session('success') }}</div>
            @endif
            @if($errors->any())
                <div class="alert alert-danger shadow-sm">
                    <ul class="mb-0 small">
                        @foreach($errors->all() as $error)
                            <li>{{ $error }}</li>
                        @endforeach
                    </ul>
                </div>
            @endif

            <div class="row g-4 align-items-start">
                <div class="col-12 col-xl-8">
                    <section class="card glass-card mb-4">
                        <div class="card-header d-flex justify-content-between align-items-center">
                            <div>
                                <h2 class="card-title">Alamat Pengiriman</h2>
                                <p class="text-muted small mb-0">Simpan beberapa alamat favorit dan ganti kapan saja.</p>
                            </div>
                            <button type="button" class="btn btn-outline-primary btn-sm {{ $errors->any() ? 'active' : '' }}" id="toggleAddressForm">
                                <i class="bi bi-plus-circle me-1"></i> Tambah Alamat Baru
                            </button>
                        </div>
                        <div class="card-body">
                            @if($addresses->isEmpty())
                                <div class="empty-state">
                                    <i class="bi bi-geo-alt"></i>
                                    <p class="mb-0">Belum ada alamat tersimpan. Tambahkan alamat pengiriman pertama Anda.</p>
                                </div>
                            @else
                                <div class="address-list">
                                    @foreach($addresses as $address)
                                        <form action="{{ route('checkout.address.select') }}" method="POST" class="address-card {{ optional($activeAddress)->id === $address->id ? 'is-active' : '' }}">
                                            @csrf
                                            <input type="hidden" name="address_id" value="{{ $address->id }}">
                                            <div class="address-card__meta">
                                                <span class="badge rounded-pill {{ $address->is_default ? 'text-bg-success' : 'text-bg-light' }}">
                                                    {{ $address->label ? e($address->label) : 'Alamat' }}
                                                </span>
                                                @if($address->is_default)
                                                    <span class="default-chip">Default</span>
                                                @endif
                                            </div>
                                            <h3>{{ $address->receiver_name }}</h3>
                                            <p class="mb-1">{{ $address->phone }}</p>
                                            <p class="text-muted mb-3">{{ $address->address_line }}, {{ $address->city }}{{ $address->postal_code ? ', ' . $address->postal_code : '' }}</p>
                                            <div class="address-card__actions">
                                                <button type="submit" name="action" value="select" class="btn btn-primary btn-sm">
                                                    Gunakan Alamat Ini
                                                </button>
                                                @if(!$address->is_default)
                                                    <button type="submit" name="action" value="make_default" class="btn btn-link btn-sm text-decoration-none">
                                                        Jadikan Default
                                                    </button>
                                                @endif
                                            </div>
                                        </form>
                                    @endforeach
                                </div>
                            @endif

                            <div class="address-form-wrapper collapse {{ $errors->any() ? 'show' : '' }}" id="newAddressForm">
                                <hr>
                                <h3 class="h5 mb-3">Alamat Baru</h3>
                                <form action="{{ route('checkout.address') }}" method="POST" class="address-form">
                                    @csrf
                                    <div class="row g-3">
                                        <div class="col-md-6">
                                            <label class="form-label">Label Alamat <span class="text-muted">(Opsional)</span></label>
                                            <input type="text" name="label" class="form-control" placeholder="Rumah, Kantor, dll" value="{{ old('label') }}">
                                            @error('label')
                                                <div class="text-danger small mt-1">{{ $message }}</div>
                                            @enderror
                                        </div>
                                        <div class="col-md-6">
                                            <label class="form-label">Nama Penerima</label>
                                            <input type="text" name="receiver_name" class="form-control" value="{{ old('receiver_name') }}" required>
                                            @error('receiver_name')
                                                <div class="text-danger small mt-1">{{ $message }}</div>
                                            @enderror
                                        </div>
                                        <div class="col-md-6">
                                            <label class="form-label">Nomor Telepon</label>
                                            <input type="text" name="phone" class="form-control" value="{{ old('phone') }}" required>
                                            @error('phone')
                                                <div class="text-danger small mt-1">{{ $message }}</div>
                                            @enderror
                                        </div>
                                        <div class="col-12">
                                            <label class="form-label">Alamat Lengkap</label>
                                            <textarea name="address_line" rows="3" class="form-control" required>{{ old('address_line') }}</textarea>
                                            @error('address_line')
                                                <div class="text-danger small mt-1">{{ $message }}</div>
                                            @enderror
                                        </div>
                                        <div class="col-md-6">
                                            <label class="form-label">Kota</label>
                                            <input type="text" name="city" class="form-control" value="{{ old('city') }}" required>
                                            @error('city')
                                                <div class="text-danger small mt-1">{{ $message }}</div>
                                            @enderror
                                        </div>
                                        <div class="col-md-6">
                                            <label class="form-label">Kode Pos</label>
                                            <input type="text" name="postal_code" class="form-control" value="{{ old('postal_code') }}">
                                            @error('postal_code')
                                                <div class="text-danger small mt-1">{{ $message }}</div>
                                            @enderror
                                        </div>
                                        <div class="col-12 d-flex align-items-center">
                                            <input type="checkbox" class="form-check-input me-2" name="set_as_default" id="setAsDefault" {{ old('set_as_default') ? 'checked' : '' }}>
                                            <label class="form-check-label" for="setAsDefault">Jadikan sebagai alamat default</label>
                                        </div>
                                        @error('set_as_default')
                                            <div class="col-12 text-danger small">{{ $message }}</div>
                                        @enderror
                                        <div class="col-12">
                                            <button type="submit" class="btn btn-primary">Simpan Alamat</button>
                                        </div>
                                    </div>
                                </form>
                            </div>
                        </div>
                    </section>

                    <section class="card glass-card">
                        <div class="card-header">
                            <h2 class="card-title mb-0">Detail Pesanan</h2>
                        </div>
                        <div class="card-body">
                            @if(isset($buyNow))
                                <article class="order-item">
                                    <img src="{{ $buyNow['image'] ?? $placeholderImage }}" alt="{{ $buyNow['product_name'] }}">
                                    <div>
                                        <h3>{{ $buyNow['product_name'] }}</h3>
                                        @if(!empty($buyNow['variant_summary']))
                                            <p class="text-muted mb-1">{{ $buyNow['variant_summary'] }}</p>
                                        @endif
                                        <p class="text-muted mb-1">Qty {{ $buyNow['quantity'] }} × {{ $formatCurrency($buyNow['price']) }}</p>
                                        <strong>{{ $formatCurrency($buyNow['subtotal']) }}</strong>
                                    </div>
                                </article>
                            @elseif($cart)
                                @foreach($cart->items as $item)
                                    @php
                                        $imageUrl = $resolveProductImage($item->product);
                                        $variants = $item->variant_labels ? implode(' · ', $item->variant_labels) : null;
                                    @endphp
                                    <article class="order-item">
                                        <img src="{{ $imageUrl }}" alt="{{ $item->product->name }}" onerror="this.onerror=null;this.src='{{ $placeholderImage }}';">
                                        <div>
                                            <h3>{{ $item->product->name }}</h3>
                                            @if($variants)
                                                <p class="text-muted mb-1">{{ $variants }}</p>
                                            @endif
                                            <p class="text-muted mb-1">Qty {{ $item->quantity }} × {{ $formatCurrency($item->unit_price) }}</p>
                                            <strong>{{ $formatCurrency($item->subtotal) }}</strong>
                                        </div>
                                    </article>
                                @endforeach
                            @endif
                        </div>
                    </section>
                </div>

                <aside class="col-12 col-xl-4">
                    <section class="card glass-card sticky-top summary-card">
                        <div class="card-body">
                            <h2 class="card-title mb-3">Ringkasan Pembayaran</h2>

                            <div class="shipping-summary mb-4">
                                <div class="shipping-header">
                                    <span class="badge rounded-pill text-bg-light">Alamat Aktif</span>
                                </div>
                                @if($activeAddress)
                                    <p class="mb-1 fw-semibold">{{ $activeAddress->receiver_name }}</p>
                                    <p class="mb-1 text-muted">{{ $activeAddress->phone }}</p>
                                    <p class="mb-0 text-muted">{{ $activeAddress->address_line }}, {{ $activeAddress->city }}{{ $activeAddress->postal_code ? ', ' . $activeAddress->postal_code : '' }}</p>
                                @else
                                    <p class="text-muted mb-0">Tambahkan alamat baru agar pesanan dapat dikirim.</p>
                                @endif
                            </div>

                            <form action="{{ route('checkout.store') }}" method="POST" class="payment-form">
                                @csrf
                                <div class="payment-options mb-4">
                                    <label class="payment-option">
                                        <input type="radio" name="payment_method" value="gopay" required>
                                        <span>
                                            <strong>Gopay</strong>
                                            <small>Saldo GoPay akan terpotong otomatis</small>
                                        </span>
                                    </label>
                                    <label class="payment-option">
                                        <input type="radio" name="payment_method" value="shopeepay">
                                        <span>
                                            <strong>ShopeePay</strong>
                                            <small>Scan atau konfirmasi di aplikasi Shopee</small>
                                        </span>
                                    </label>
                                    <label class="payment-option">
                                        <input type="radio" name="payment_method" value="qris">
                                        <span>
                                            <strong>QRIS</strong>
                                            <small>Bayar melalui aplikasi bank favoritmu</small>
                                        </span>
                                    </label>
                                    <label class="payment-option">
                                        <input type="radio" name="payment_method" value="va">
                                        <span>
                                            <strong>Virtual Account</strong>
                                            <small>Pembayaran melalui ATM atau mobile banking</small>
                                        </span>
                                    </label>
                                </div>

                                <ul class="cost-breakdown list-unstyled mb-4">
                                    <li class="d-flex justify-content-between mb-2">
                                        <span>Subtotal</span>
                                        <span>{{ isset($buyNow) ? $formatCurrency($buyNow['subtotal']) : $formatCurrency($cart->subtotal) }}</span>
                                    </li>
                                    <li class="d-flex justify-content-between mb-2 text-muted">
                                        <span>Pajak</span>
                                        <span>Dihitung saat pembayaran</span>
                                    </li>
                                    <li class="d-flex justify-content-between mb-2 text-success">
                                        <span>Pengiriman</span>
                                        <span>Gratis</span>
                                    </li>
                                </ul>

                                <div class="total-line d-flex justify-content-between align-items-center mb-4">
                                    <span>Total Tagihan</span>
                                    <strong>{{ isset($buyNow) ? $formatCurrency($buyNow['subtotal']) : $formatCurrency($cart->subtotal) }}</strong>
                                </div>

                                <button type="submit" class="btn btn-primary w-100 btn-lg" {{ $activeAddress ? '' : 'disabled' }}>
                                    Lanjut Bayar
                                </button>
                            </form>
                            <p class="security-note text-muted small mt-3"><i class="bi bi-shield-check me-1"></i> Pembayaran Anda terenkripsi dan aman. Kami tidak menyimpan detail kartu atau dompet digital Anda.</p>
                        </div>
                    </section>
                </aside>
            </div>
        </div>
    </main>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
    <script>
        const toggleButton = document.getElementById('toggleAddressForm');
        const addressForm = document.getElementById('newAddressForm');

        if (toggleButton && addressForm) {
            const syncToggleState = () => {
                const isOpen = addressForm.classList.contains('show');
                toggleButton.classList.toggle('active', isOpen);
                toggleButton.innerHTML = isOpen
                    ? '<i class="bi bi-dash-circle me-1"></i> Tutup Form Alamat'
                    : '<i class="bi bi-plus-circle me-1"></i> Tambah Alamat Baru';
            };

            syncToggleState();

            toggleButton.addEventListener('click', () => {
                addressForm.classList.toggle('show');
                syncToggleState();
            });
        }
    </script>
</body>
</html>
