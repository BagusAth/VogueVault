@extends('layouts.app')

@section('content')
<div class="container">
    <h2 class="text-xl font-bold mb-4">Keranjang Belanja</h2>

    @if($cart->items->isEmpty())
        <p>Keranjang kosong. <a href="{{ route('home') }}" class="text-blue-500">Belanja sekarang</a></p>
    @else
        <table class="w-full border">
            <thead>
                <tr class="bg-gray-100">
                    <th class="p-2">Produk</th>
                    <th class="p-2">Harga</th>
                    <th class="p-2">Jumlah</th>
                    <th class="p-2">Subtotal</th>
                    <th class="p-2">Aksi</th>
                </tr>
            </thead>
            <tbody>
                @foreach($cart->items as $item)
                    <tr class="border-b">
                        <td class="p-2">{{ $item->product->name }}</td>
                        <td class="p-2">Rp{{ number_format($item->unit_price,0,',','.') }}</td>
                        <td class="p-2">
                            <form action="{{ route('cart.update', $item) }}" method="POST" class="flex">
                                @csrf
                                <input type="number" name="quantity" value="{{ $item->quantity }}" min="1" class="w-16 border p-1 mr-2">
                                <button type="submit" class="bg-blue-500 text-white px-2 rounded">Update</button>
                            </form>
                        </td>
                        <td class="p-2">Rp{{ number_format($item->subtotal,0,',','.') }}</td>
                        <td class="p-2">
                            <form action="{{ route('cart.remove', $item) }}" method="POST">
                                @csrf
                                @method('DELETE')
                                <button type="submit" class="bg-red-500 text-white px-2 rounded">Hapus</button>
                            </form>
                        </td>
                    </tr>
                @endforeach
            </tbody>
        </table>

        <div class="mt-4 flex justify-between">
            <form action="{{ route('cart.clear') }}" method="POST">
                @csrf
                @method('DELETE')
                <button type="submit" class="bg-gray-400 text-white px-3 py-1 rounded">Kosongkan Keranjang</button>
            </form>

            <div class="text-right">
                <p class="font-bold text-lg">Total: Rp{{ number_format($cart->subtotal,0,',','.') }}</p>
                <a href="{{ route('checkout') }}" class="bg-green-500 text-white px-4 py-2 rounded mt-2 inline-block">Checkout</a>
            </div>
        </div>
    @endif
</div>
@endsection
