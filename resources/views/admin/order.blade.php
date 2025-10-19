<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Admin · Orders - VogueVault</title>
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.10.0/font/bootstrap-icons.css">
    <link rel="stylesheet" href="{{ asset('css/admin/product.css') }}">
    <link rel="stylesheet" href="{{ asset('css/admin/order.css') }}">
</head>
<body>
    <div class="layout">
        @include('admin.partials.sidebar', ['active' => 'orders'])

        <main class="content">
            <header class="content-header">
                <div>
                    <h1 class="content-title">Orders</h1>
                    <p class="content-subtitle">Monitor customer transactions and manage fulfillment statuses.</p>
                </div>
            </header>

            @if(session('success'))
                <div class="order-alert success">{{ session('success') }}</div>
            @endif

            <section class="order-panel">
                @if($orders->isEmpty())
                    <div class="order-empty">
                        <i class="bi bi-inboxes"></i>
                        <p>No orders have been placed yet.</p>
                    </div>
                @else
                    <div class="order-table-wrapper">
                        <table class="order-table">
                            <thead>
                                <tr>
                                    <th>Order</th>
                                    <th>Customer</th>
                                    <th>Products</th>
                                    <th>Date</th>
                                    <th>Status</th>
                                    <th class="text-right">Amount</th>
                                </tr>
                            </thead>
                            <tbody>
                                @foreach($orders as $order)
                                    @php
                                        $amount = $order->total_amount ?? $order->subtotal ?? 0;
                                        $orderNumber = $order->order_number ?: sprintf('#%06d', $order->id);
                                    @endphp
                                    <tr>
                                        <td>
                                            <div class="order-ident">
                                                <span class="order-number">{{ $orderNumber }}</span>
                                                <span class="order-meta">{{ optional($order->created_at)->format('d M Y • H:i') }}</span>
                                                @if($order->payment_method)
                                                    <span class="order-meta muted">{{ strtoupper($order->payment_method) }}</span>
                                                @endif
                                            </div>
                                        </td>
                                        <td>
                                            <div class="order-customer">
                                                <span>{{ $order->user->name ?? 'Unknown customer' }}</span>
                                                <span class="order-meta">{{ $order->user->email ?? '—' }}</span>
                                            </div>
                                        </td>
                                        <td>
                                            <div class="order-products">
                                                @forelse($order->items as $item)
                                                    <div class="order-product">
                                                        <div class="order-product-main">
                                                            <span class="order-product-name">{{ $item->product_name ?? $item->product?->name ?? 'Unknown product' }}</span>
                                                            <span class="order-product-qty">×{{ $item->quantity }}</span>
                                                        </div>
                                                        @if(!empty($item->variant_labels))
                                                            <div class="order-product-variants">
                                                                {{ implode(', ', $item->variant_labels) }}
                                                            </div>
                                                        @endif
                                                    </div>
                                                @empty
                                                    <span class="order-meta">No items</span>
                                                @endforelse
                                            </div>
                                        </td>
                                        <td>{{ optional($order->created_at)->format('d M Y') }}</td>
                                        <td>
                                            <form action="{{ route('admin.orders.updateStatus', $order) }}" method="POST" class="status-form">
                                                @csrf
                                                @method('PATCH')
                                                <select name="status" class="status-select status-{{ $order->status }}" onchange="this.form.submit()">
                                                    @foreach($statusOptions as $value => $label)
                                                        <option value="{{ $value }}" @selected($order->status === $value)>
                                                            {{ $label }}
                                                        </option>
                                                    @endforeach
                                                </select>
                                                <noscript>
                                                    <button type="submit" class="status-submit">Update</button>
                                                </noscript>
                                            </form>
                                        </td>
                                        <td class="text-right order-amount">Rp {{ number_format($amount, 0, ',', '.') }}</td>
                                    </tr>
                                @endforeach
                            </tbody>
                        </table>
                    </div>

                    <div class="order-pagination">
                        {{ $orders->links() }}
                    </div>
                @endif
            </section>
        </main>
    </div>

    <script>
        document.addEventListener('DOMContentLoaded', function () {
            const selects = document.querySelectorAll('.status-select');

            const statusClassMap = {
                pending: 'status-pending',
                processing: 'status-processing',
                shipped: 'status-shipped',
                delivered: 'status-delivered',
                cancelled: 'status-cancelled'
            };

            selects.forEach(select => {
                const applyState = () => {
                    select.classList.forEach(cls => {
                        if (cls.startsWith('status-')) {
                            select.classList.remove(cls);
                        }
                    });
                    const state = statusClassMap[select.value];
                    if (state) {
                        select.classList.add(state);
                    }
                };

                select.addEventListener('change', applyState);
                applyState();
            });
        });
    </script>
</body>
</html>
