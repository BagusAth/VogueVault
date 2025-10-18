<!DOCTYPE html>
<html lang="en">
<head>
	<meta charset="UTF-8">
	<meta name="viewport" content="width=device-width, initial-scale=1.0">
	<title>My Orders · VogueVault</title>

	<link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css">
	<link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.10.0/font/bootstrap-icons.css">
	<link rel="stylesheet" href="{{ asset('css/navbar.css') }}">
	<link rel="stylesheet" href="{{ asset('css/order.css') }}">
</head>
<body>
	@include('partials.navbar')

	<main class="orders-main">
		@php
			$placeholderImage = asset('images/placeholder_img.jpg');

			$resolveProductImage = static function ($product) use ($placeholderImage) {
				if (!$product) {
					return $placeholderImage;
				}

				$images = $product->images ?? [];
				if (is_string($images)) {
					$decoded = json_decode($images, true);
					if (json_last_error() === JSON_ERROR_NONE && is_array($decoded)) {
						$images = $decoded;
					} else {
						$images = [$images];
					}
				}

				$images = collect($images)
					->flatten()
					->filter(fn ($value) => is_string($value) && $value !== '')
					->values();

				$rawImage = $images->first();
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
		<section class="orders-hero text-center py-5">
			<div class="container">
				<h1 class="orders-title">Order History</h1>
				<p class="orders-subtitle">Track every purchase and stay updated with real-time order statuses.</p>
			</div>
		</section>

		<section class="orders-content py-4">
			<div class="container">
				@if($orders->isEmpty())
					<div class="orders-empty text-center">
						<div class="orders-empty__icon mb-3">
							<i class="bi bi-receipt"></i>
						</div>
						<h2 class="orders-empty__title">You haven’t placed any orders yet</h2>
						<p class="orders-empty__subtitle">Start shopping and your orders will appear here automatically.</p>
						<a href="{{ route('home') }}" class="btn btn-primary rounded-pill px-4">Browse Products</a>
					</div>
				@else
					<div class="orders-list">
						@foreach($orders as $order)
							@php
								$statusSlug = Illuminate\Support\Str::slug($order->status ?? 'unknown');
								$orderNumber = $order->order_number ?? 'ORD-' . str_pad((string) $order->id, 6, '0', STR_PAD_LEFT);
								$placedAt = optional($order->created_at)->format('d M Y, H:i');
								$totalValue = $order->grand_total ?? $order->total_amount ?? $order->subtotal ?? 0;
								$isUnpaid = strtolower($order->payment_status ?? '') !== 'paid';
								$paymentUrl = $isUnpaid ? route('checkout.payment', $order) : null;
							@endphp
							<article class="order-card {{ $isUnpaid ? 'order-card--actionable' : '' }}" data-order-id="{{ $order->id }}" data-status-url="{{ route('orders.status', $order) }}" @if($paymentUrl) data-payment-url="{{ $paymentUrl }}" @endif>
								<header class="order-card__header">
									<div class="order-card__identity">
										<span class="order-card__number">Order {{ $orderNumber }}</span>
										<span class="order-card__date">Placed {{ $placedAt ?? 'N/A' }}</span>
									</div>
									<div class="order-card__status-group">
										<span class="order-status-badge status-{{ $statusSlug }}" data-order-status>{{ ucfirst($order->status ?? 'Unknown') }}</span>
										<span class="order-payment" data-payment-status>Payment: {{ ucfirst($order->payment_status ?? 'Unknown') }}</span>
										<span class="order-updated" data-order-updated>Updated {{ optional($order->updated_at)->diffForHumans() }}</span>
										@if($isUnpaid)
											<a href="{{ $paymentUrl }}" class="order-card__cta btn btn-sm btn-outline-success">Bayar Sekarang</a>
										@endif
									</div>
								</header>

								<div class="order-card__body">
									<div class="order-card__summary">
										<span>Total</span>
										<strong>Rp {{ number_format((float) $totalValue, 0, ',', '.') }}</strong>
									</div>

									<ul class="order-items list-unstyled mt-3">
										@foreach($order->items as $item)
											@php
												$product = $item->product;
												$image = $resolveProductImage($product);
											@endphp
											<li class="order-item d-flex align-items-start gap-3">
												<div class="order-item__thumb">
													<img src="{{ $image }}" alt="{{ $item->product_name }}" onerror="this.onerror=null;this.src='{{ $placeholderImage }}';">
												</div>
												<div class="order-item__details flex-grow-1">
													<div class="order-item__top">
														<h3 class="order-item__name">{{ $item->product_name }}</h3>
														<span class="order-item__price">Rp {{ number_format((float) ($item->total_price ?? $item->subtotal ?? $item->unit_price * $item->quantity), 0, ',', '.') }}</span>
													</div>
													@php
														$variants = collect($item->selected_attributes ?? $item->product_attributes ?? [])
															->filter(fn ($value) => $value !== null && $value !== '')
															->map(function ($value, $key) {
																$label = ucwords(str_replace(['_', '-'], ' ', (string) $key));
																return $label . ': ' . $value;
															})
															->values()
															->all();
													@endphp
													<div class="order-item__meta">
														<span class="order-item__quantity">Qty: {{ $item->quantity }}</span>
														<span class="order-item__unit">@ Rp {{ number_format((float) ($item->unit_price ?? 0), 0, ',', '.') }}</span>
														@if(!empty($variants))
															<span class="order-item__variants">{{ implode(' · ', $variants) }}</span>
														@endif
													</div>
												</div>
											</li>
										@endforeach
									</ul>
								</div>
							</article>
						@endforeach
					</div>
				@endif
			</div>
		</section>
	</main>

	<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
	<script src="{{ asset('js/order.js') }}"></script>
</body>
</html>
