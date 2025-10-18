<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.10.0/font/bootstrap-icons.css">
    <link rel="stylesheet" href="{{ asset('css/navbar.css') }}">
    <link rel="stylesheet" href="{{ asset('css/product.css') }}">
</head>
<body>
    @include('partials.navbar')

    <div class="container my-4">
        <div class="row g-4">

         {{-- Tampilkan error kalau ada --}}
        @if(session('error'))
            <div class="alert alert-danger">
                {{ session('error') }}
            </div>
        @endif
        @if(session('success'))
            <div class="alert alert-success">
                {{ session('success') }}
            </div>
        @endif


            {{-- Kiri: ringkasan pesanan --}}
            <div class="col-lg-8 bg-white p-4 rounded shadow-sm mb-4 mb-lg-0">

                <h2 class="fw-bold mb-4">Pesanan Anda</h2>
                {{-- Mode Buy Now --}}
                @if(isset($buyNow))
                    <div class="d-flex border-bottom py-3">
                        <img src="{{ $buyNow['image'] ?? 'https://via.placeholder.com/80' }}"
                            class="me-3" width="80" height="80" style="object-fit:cover">
                        <div>
                            <p class="fw-semibold mb-1">{{ $buyNow['product_name'] }}</p>
                            <p class="mb-1">
                                Qty: {{ $buyNow['quantity'] }} x Rp{{ number_format($buyNow['price'],0,',','.') }}
                            </p>
                            <p class="fw-bold">
                                Subtotal: Rp{{ number_format($buyNow['subtotal'],0,',','.') }}
                            </p>
                        </div>
                    </div>
                @elseif($cart)
                    {{-- Mode dari keranjang --}}
                    @foreach($cart->items as $item)
                        <div class="d-flex border-bottom py-3">
                            <img src="{{ $item->product->image_url }}"
                                class="me-3 rounded" width="80" height="80" style="object-fit:cover">
                            <div>
                                <p class="fw-semibold mb-1">{{ $item->product->name }}</p>
                                <p class="text-muted small mb-1">{{ $item->product->short_description }}</p>
                                <p class="mb-1">
                                    Qty: {{ $item->quantity }} x Rp{{ number_format($item->unit_price,0,',','.') }}
                                </p>
                                <p class="fw-bold">
                                    Subtotal: Rp{{ number_format($item->subtotal,0,',','.') }}
                                </p>
                            </div>
                        </div>
                    @endforeach
                @else
                    <p class="text-muted">Tidak ada item untuk checkout.</p>
                @endif
            </div>

            {{-- Kanan: alamat + pembayaran --}}
            <div class="col-lg-4 bg-white p-4 rounded shadow-sm ms-lg-0">
                <form action="{{ route('checkout.address') }}" method="POST" class="mb-4">
                    @csrf
                    <h5 class="fw-bold">Alamat Pengiriman</h5>
                    <input type="text" name="receiver_name" placeholder="Nama" 
                        class="form-control my-2"
                        value="{{old('receiver_name', $address['receiver_name'] ?? '') }}">

                    <input type="text" name="phone" placeholder="No HP" 
                        class="form-control my-2"
                        value="{{old('phone', $address['phone'] ?? '') }}">

                    <textarea name="address_line" placeholder="Alamat lengkap" 
                        class="form-control my-2">{{ old('address_line', $address['address_line'] ?? '') }}</textarea>

                    <input type="text" name="city" placeholder="Kota" 
                        class="form-control my-2"
                        value="{{old('city', $address['city'] ?? '') }}">

                    <input type="text" name="postal_code" placeholder="Kode Pos" 
                        class="form-control my-2"
                        value="{{ old('postal_code', $address['postal_code'] ?? '') }}">

                    <button type="submit" class="btn btn-secondary w-100">Simpan</button>
                </form>

                <form action="{{ route('checkout.store') }}" method="POST">
                    @csrf
                    <h5 class="fw-bold">Metode Pembayaran</h5>
                    <div class="form-check">
                        <input type="radio" name="payment_method" value="gopay" class="form-check-input" id="payGopay">
                        <label for="payGopay" class="form-check-label">Gopay</label>
                    </div>
                    <div class="form-check">
                        <input type="radio" name="payment_method" value="shopeepay" class="form-check-input" id="payShopee">
                        <label for="payShopee" class="form-check-label">ShopeePay</label>
                    </div>
                    <div class="form-check">
                        <input type="radio" name="payment_method" value="qris" class="form-check-input" id="payQris">
                        <label for="payQris" class="form-check-label">QRIS</label>
                    </div>
                    <div class="form-check">
                        <input type="radio" name="payment_method" value="va" class="form-check-input" id="payVA">
                        <label for="payVA" class="form-check-label">Virtual Account</label>
                    </div>

                    <div class="mt-3 fw-bold">
                        Total Tagihan: Rp
                        @if(isset($buyNow))
                            {{ number_format($buyNow['subtotal'],0,',','.') }}
                        @else
                            {{ number_format($cart->subtotal,0,',','.') }}
                        @endif
                    </div>

                    <button type="submit" class="btn btn-success w-100 mt-3">Bayar Sekarang</button>
                </form>
            </div>
        </div>
    </div>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>
