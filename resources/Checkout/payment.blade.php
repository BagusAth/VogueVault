@extends('layouts.app')

@section('content')
<div class="container max-w-xl mx-auto bg-white p-6 rounded shadow">
    <h2 class="text-lg font-bold mb-4">Pembayaran</h2>

    <p>Pay before: {{ $order->expires_at->format('d M Y, H:i') }}</p>

    <div class="border p-4 rounded my-4">
        <p class="text-gray-500">Order Number</p>
        <p class="text-xl font-bold">{{ $order->order_number }}</p>
    </div>

    <p class="text-lg font-semibold">Total Bill: Rp{{ number_format($order->grand_total,0,',','.') }}</p>

    <div class="mt-4 space-x-4">
        <button class="bg-gray-200 px-4 py-2 rounded">How to pay</button>
        <button id="checkStatus" class="bg-gray-200 px-4 py-2 rounded">Payment status</button>
    </div>

    <div id="statusResult" class="mt-4 text-sm text-gray-600"></div>
</div>

<script>
document.getElementById('checkStatus').addEventListener('click', function() {
    fetch("{{ route('orders.status', $order) }}")
        .then(res => res.json())
        .then(data => {
            document.getElementById('statusResult').innerText = "Status: " + data.payment_status;
        });
});
</script>
@endsection