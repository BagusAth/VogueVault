<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Keranjang Belanja</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.10.0/font/bootstrap-icons.css">
    <link rel="stylesheet" href="{{ asset('css/navbar.css') }}">
    <link rel="stylesheet" href="{{ asset('css/product.css') }}">
</head>
<body>
@include('partials.navbar')

<div class="container my-4">
    <h2 class="fw-bold mb-4">Keranjang Belanja</h2>

    @if($cart->items->isEmpty())
        <p>Keranjang kosong. 
            <a href="{{ route('home') }}" class="text-primary">Belanja sekarang</a>
        </p>
    @else
        @foreach($cart->items as $item)
            <div class="bg-white p-3 rounded shadow-sm mb-3 d-flex align-items-center">
                {{-- Gambar produk --}}
                <img src="{{ $item->product->images[0] ?? 'https://via.placeholder.com/80' }}" 
                     alt="{{ $item->product->name }}" 
                     class="rounded me-3" 
                     style="width:80px;height:80px;object-fit:cover;">

                {{-- Detail produk --}}
                <div class="flex-grow-1">
                    <h5 class="mb-1">{{ $item->product->name }}</h5>
                    <p class="text-muted mb-1">Rp{{ number_format($item->unit_price,0,',','.') }}</p>

                    {{-- Form update qty --}}
                    <form action="{{ route('cart.update', $item) }}" method="POST" class="d-flex align-items-center gap-2">
                        @csrf
                        <input type="number" name="quantity" value="{{ $item->quantity }}" min="1" 
                               class="form-control form-control-sm" style="width:70px;">
                        <button type="submit" class="btn btn-sm btn-outline-secondary">Update</button>
                    </form>

                    <p class="fw-bold mt-2 mb-0">Subtotal: Rp{{ number_format($item->subtotal,0,',','.') }}</p>
                </div>

                {{-- Tombol hapus di kanan --}}
                <form action="{{ route('cart.remove', $item) }}" method="POST" class="ms-3">
                    @csrf
                    @method('DELETE')
                    <button type="submit" class="btn btn-sm btn-outline-danger">Hapus</button>
                </form>
            </div>
        @endforeach

        {{-- Ringkasan total --}}
        <div class="text-end mt-4">
            <form action="{{ route('cart.clear') }}" method="POST" class="d-inline">
                @csrf
                @method('DELETE')
                <button type="submit" class="btn btn-outline-dark">Kosongkan Keranjang</button>
            </form>

            <p class="fw-bold fs-5 mt-3">Total: Rp{{ number_format($cart->subtotal,0,',','.') }}</p>
            <a href="{{ route('checkout.index') }}" class="btn btn-success px-4">Checkout</a>
        </div>
    @endif
</div>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>
