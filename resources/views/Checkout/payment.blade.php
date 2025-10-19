<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <title>Order Payment • VogueVault</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.10.0/font/bootstrap-icons.css">
    <link rel="stylesheet" href="{{ asset('css/navbar.css') }}">
    <link rel="stylesheet" href="{{ asset('css/payment.css') }}">
</head>
@php
    $isPaid = $order->payment_status === 'paid';
    $formattedTotal = 'Rp' . number_format($order->grand_total, 0, ',', '.');
    $paymentMethod = strtoupper($order->payment_method ?? 'ONLINE');
@endphp
<body class="payment-body">
    @include('partials.navbar')

    <main class="payment-wrapper py-5">
        <div class="container">
            <div class="row justify-content-center">
                <div class="col-xl-8">
                    <section class="payment-card">
                        <header class="payment-header">
                            <div>
                                <p class="eyebrow mb-1">Payment Confirmation</p>
                                <h1 class="payment-title">Order {{ $order->order_number }}</h1>
                            </div>
                            <span class="status-pill {{ $isPaid ? 'is-paid' : 'is-unpaid' }}" data-status-pill>
                                <i class="bi {{ $isPaid ? 'bi-check-circle' : 'bi-clock-history' }} me-2"></i>
                                <span data-status-text>{{ $isPaid ? 'Paid' : 'Unpaid' }}</span>
                            </span>
                        </header>

                        <div class="payment-meta">
                            <div class="meta-block">
                                <span class="meta-label">Amount Due</span>
                                <span class="meta-value text-success" data-total-label>{{ $formattedTotal }}</span>
                            </div>
                            <div class="meta-block">
                                <span class="meta-label">Method</span>
                                <span class="meta-value">{{ $paymentMethod }}</span>
                            </div>
                            <div class="meta-block meta-block--deadline">
                                <span class="meta-label">Pay Before</span>
                                <span class="meta-value" data-deadline="{{ optional($order->expires_at)->format('d M Y, H:i') }}" data-countdown="{{ $remainingSeconds ?? '' }}">
                                    @if($order->expires_at)
                                        {{ $order->expires_at->format('d M Y, H:i') }} WIB
                                    @else
                                        No payment deadline
                                    @endif
                                </span>
                                @if($order->expires_at)
                                    <span class="countdown" data-countdown-label>
                                        @if($isPaid)
                                            Payment confirmed
                                        @elseif($isExpired)
                                            Payment window expired
                                        @else
                                            Time remaining: <span data-countdown-text>—</span>
                                        @endif
                                    </span>
                                @endif
                            </div>
                        </div>

                        <article class="payment-summary">
                            <h2>Order Details</h2>
                            <dl class="summary-list">
                                <div class="summary-item">
                                    <dt>Order Number</dt>
                                    <dd>{{ $order->order_number }}</dd>
                                </div>
                                <div class="summary-item">
                                    <dt>Order Status</dt>
                                    <dd data-order-status>{{ ucfirst($order->status) }}</dd>
                                </div>
                                <div class="summary-item">
                                    <dt>Payment Status</dt>
                                    <dd data-order-payment>{{ $isPaid ? 'Paid' : 'Unpaid' }}</dd>
                                </div>
                                @if($order->expires_at)
                                    <div class="summary-item">
                                        <dt>Payment Deadline</dt>
                                        <dd>{{ $order->expires_at->translatedFormat('d F Y, H:i') }} WIB</dd>
                                    </div>
                                @endif
                            </dl>
                        </article>

                        <div class="payment-actions">
                            <button type="button" class="btn btn-outline-secondary" data-action="instructions" data-bs-toggle="modal" data-bs-target="#instructionsModal">
                                <i class="bi bi-journal-text me-2"></i>How to Pay
                            </button>
                            <button type="button" class="btn btn-success" data-action="pay" {{ ($isPaid || $isExpired) ? 'disabled' : '' }}>
                                <span class="spinner-border spinner-border-sm me-2 d-none" role="status" aria-hidden="true"></span>
                                <i class="bi bi-credit-card me-2"></i>Pay Now
                            </button>
                            <button type="button" class="btn btn-outline-primary" data-action="refresh">
                                <i class="bi bi-arrow-repeat me-2"></i>Check Status
                            </button>
                        </div>

                        <div class="payment-feedback" data-feedback aria-live="polite"></div>

                        <footer class="payment-footer">
                            <p><i class="bi bi-shield-check me-2"></i>Your payment is processed securely with encryption. We never store your card or digital wallet details.</p>
                            <p><i class="bi bi-info-circle me-2"></i>We forward your order to fulfilment as soon as the payment is confirmed.</p>
                        </footer>
                    </section>
                </div>
            </div>
        </div>
    </main>

    <div class="modal fade" id="instructionsModal" tabindex="-1" aria-labelledby="instructionsModalLabel" aria-hidden="true">
        <div class="modal-dialog modal-dialog-centered">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title" id="instructionsModalLabel">Payment Guide</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <div class="modal-body">
                    <ol class="instruction-list">
                        <li>Choose your preferred payment method at checkout.</li>
                        <li>Follow the instructions in your payment app or bank service to complete the transaction.</li>
                        <li>Click <strong>Pay Now</strong> on this page to confirm your payment.</li>
                        <li>The status updates to <strong>Paid</strong> automatically.</li>
                    </ol>
                    <p class="text-muted small mb-0">Need help? Reach out to our support team and we'll assist you.</p>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-primary" data-bs-dismiss="modal">Got it</button>
                </div>
            </div>
        </div>
    </div>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
    <script>
        (function() {
            const csrfToken = document.querySelector('meta[name="csrf-token"]').getAttribute('content');
            const endpoints = {
                complete: "{{ route('checkout.payment.complete', $order) }}",
                status: "{{ route('orders.status', $order) }}",
            };

            const statusPill = document.querySelector('[data-status-pill]');
            const statusText = document.querySelector('[data-status-text]');
            const orderStatus = document.querySelector('[data-order-status]');
            const paymentStatus = document.querySelector('[data-order-payment]');
            const feedback = document.querySelector('[data-feedback]');
            const payButton = document.querySelector('[data-action="pay"]');
            const refreshButton = document.querySelector('[data-action="refresh"]');
            const spinner = payButton ? payButton.querySelector('.spinner-border') : null;
            const countdownLabel = document.querySelector('[data-countdown-label]');
            const countdownText = document.querySelector('[data-countdown-text]');
            const countdownSource = document.querySelector('[data-countdown]');

            const showFeedback = (message, variant = 'info') => {
                if (!feedback) return;
                feedback.textContent = message;
                feedback.className = `payment-feedback is-${variant}`;
            };

            const setPaidState = (message = 'Payment confirmed successfully.', statusValue = 'processing') => {
                statusPill?.classList.remove('is-unpaid');
                statusPill?.classList.add('is-paid');
                if (statusText) statusText.textContent = 'Paid';
                if (orderStatus && statusValue) {
                    const formatted = statusValue.charAt(0).toUpperCase() + statusValue.slice(1);
                    orderStatus.textContent = formatted;
                }
                if (paymentStatus) paymentStatus.textContent = 'Paid';
                if (payButton) payButton.disabled = true;
                if (spinner) spinner.classList.add('d-none');
                if (countdownLabel) {
                    countdownLabel.textContent = 'Payment confirmed';
                }
                showFeedback(message, 'success');
            };

            const handleError = (message) => {
                showFeedback(message, 'danger');
                if (spinner) spinner.classList.add('d-none');
                if (payButton) payButton.disabled = false;
            };

            if (payButton && !payButton.disabled) {
                payButton.addEventListener('click', async () => {
                    payButton.disabled = true;
                    if (spinner) spinner.classList.remove('d-none');
                    showFeedback('Processing payment...', 'info');

                    try {
                        const response = await fetch(endpoints.complete, {
                            method: 'POST',
                            headers: {
                                'X-CSRF-TOKEN': csrfToken,
                                'Accept': 'application/json',
                                'Content-Type': 'application/json',
                                'X-Requested-With': 'XMLHttpRequest',
                            },
                            body: JSON.stringify({})
                        });

                        const payload = await response.json();

                        if (!response.ok || !payload.success) {
                            throw new Error(payload.message || 'Unable to confirm payment.');
                        }

                        setPaidState(payload.message, payload.status);
                    } catch (error) {
                        handleError(error.message || 'An unexpected error occurred.');
                    }
                });
            }

            if (refreshButton) {
                refreshButton.addEventListener('click', async () => {
                    showFeedback('Checking the latest status...', 'info');
                    try {
                        const response = await fetch(endpoints.status, {
                            headers: {
                                'Accept': 'application/json',
                                'X-Requested-With': 'XMLHttpRequest',
                            }
                        });
                        const payload = await response.json();
                        if (orderStatus && payload.status) {
                            orderStatus.textContent = payload.status.charAt(0).toUpperCase() + payload.status.slice(1);
                        }
                        if (paymentStatus && payload.payment_status) {
                            const label = payload.payment_status.charAt(0).toUpperCase() + payload.payment_status.slice(1);
                            paymentStatus.textContent = label;
                        }
                        if (payload.payment_status === 'paid') {
                            setPaidState('Payment has been confirmed.', payload.status);
                        } else {
                            showFeedback('Payment not received yet. Please complete it before the deadline.', 'warning');
                        }
                    } catch (error) {
                        handleError('Unable to fetch the latest status.');
                    }
                });
            }

            if (countdownText && countdownSource) {
                let remaining = parseInt(countdownSource.getAttribute('data-countdown'), 10);
                if (!Number.isNaN(remaining) && remaining > 0) {
                    const formatTime = (seconds) => {
                        const hours = Math.floor(seconds / 3600);
                        const minutes = Math.floor((seconds % 3600) / 60);
                        const secs = seconds % 60;
                        return `${hours.toString().padStart(2, '0')}:${minutes.toString().padStart(2, '0')}:${secs.toString().padStart(2, '0')}`;
                    };

                    countdownText.textContent = formatTime(remaining);

                    const interval = setInterval(() => {
                        remaining -= 1;
                        if (remaining <= 0) {
                            clearInterval(interval);
                            countdownText.textContent = '00:00:00';
                            if (countdownLabel && !payButton?.disabled) {
                                    countdownLabel.textContent = 'Payment window expired';
                                if (payButton) payButton.disabled = true;
                            }
                            return;
                        }
                        countdownText.textContent = formatTime(remaining);
                    }, 1000);
                }
            }

            if (@json($isExpired) && payButton) {
                payButton.disabled = true;
                showFeedback('The payment deadline has passed. Please place a new order.', 'danger');
            }

            if (@json($isPaid)) {
                showFeedback('Payment has been confirmed. Thank you!', 'success');
            }
        })();
    </script>
</body>
</html>