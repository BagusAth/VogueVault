@extends('layouts.app')

@section('content')
<div class="container grid grid-cols-3 gap-6">
    {{-- Kiri: ringkasan pesanan --}}
    <div class="col-span-2 bg-white p-4 rounded shadow">
        <h2 class="text-lg font-bold mb-4">Pesanan Anda</h2>
        @foreach($cart->items as $item)
            <div class="flex border-b py-3">
                <img src="{{ $item->product->images[0] ?? 'https://via.placeholder.com/80' }}" class="w-20 h-20 object-cover mr-4">
                <div>
                    <p class="font-semibold">{{ $item->product->name }}</p>
                    <p class="text-sm text-gray-500">{{ $item->product->short_description }}</p>
                    <p class="mt-1">Qty: {{ $item->quantity }} x Rp{{ number_format($item->unit_price,0,',','.') }}</p>
                    <p class="font-bold">Subtotal: Rp{{ number_format($item->subtotal,0,',','.') }}</p>
                </div>
            </div>
        @endforeach
    </div>

    {{-- Kanan: alamat + pembayaran --}}
    <div class="bg-white p-4 rounded shadow space-y-4">
        <form action="{{ route('checkout.address') }}" method="POST" class="space-y-2">
            @csrf
            <h3 class="font-bold">Alamat Pengiriman</h3>
            <input type="text" name="receiver_name" placeholder="Nama" class="w-full border p-2">
            <input type="text" name="phone" placeholder="No HP" class="w-full border p-2">
            <textarea name="address_line" placeholder="Alamat lengkap" class="w-full border p-2"></textarea>
            <input type="text" name="city" placeholder="Kota" class="w-full border p-2">
            <input type="text" name="postal_code" placeholder="Kode Pos" class="w-full border p-2">
            <button type="submit" class="bg-gray-200 px-4 py-2 rounded">Simpan</button>
        </form>

        <form action="{{ route('checkout.store') }}" method="POST" class="space-y-2">
            @csrf
            <h3 class="font-bold">Metode Pembayaran</h3>
            <label><input type="radio" name="payment_method" value="gopay"> Gopay</label><br>
            <label><input type="radio" name="payment_method" value="shopeepay"> Shopeepay</label><br>
            <label><input type="radio" name="payment_method" value="qris"> QRIS</label><br>
            <label><input type="radio" name="payment_method" value="va"> Virtual Account</label>

            <div class="mt-4 font-bold">
                Total Tagihan: Rp{{ number_format($cart->subtotal,0,',','.') }}
            </div>

            <button type="submit" class="bg-green-500 text-white px-4 py-2 rounded">Bayar Sekarang</button>
        </form>
    </div>
</div>
@endsection