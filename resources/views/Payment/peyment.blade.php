@extends('layouts.app')

@section('content')
<div class="container bg-white p-6 rounded shadow">
    <h2 class="text-xl font-bold mb-4">Payment</h2>
    <p>Order Number: <strong>{{ $order->order_number }}</strong></p>
    <p>Total: <strong>Rp{{ number_format($order->grand_total,0,',','.') }}</strong></p>
    <p>Payment Method: <strong>{{ ucfirst($order->payment_method) }}</strong></p>
</div>
@endsection
