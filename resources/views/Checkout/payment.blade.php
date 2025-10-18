<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <title>Pembayaran Pesanan • VogueVault</title>
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
                                <p class="eyebrow mb-1">Konfirmasi Pembayaran</p>
                                <h1 class="payment-title">Pesanan {{ $order->order_number }}</h1>
                            </div>
                            <span class="status-pill {{ $isPaid ? 'is-paid' : 'is-unpaid' }}" data-status-pill>
                                <i class="bi {{ $isPaid ? 'bi-check-circle' : 'bi-clock-history' }} me-2"></i>
                                <span data-status-text>{{ $isPaid ? 'Sudah Dibayar' : 'Belum Dibayar' }}</span>
                            </span>
                        </header>

                        <div class="payment-meta">
                            <div class="meta-block">
                                <span class="meta-label">Total Tagihan</span>
                                <span class="meta-value text-success" data-total-label>{{ $formattedTotal }}</span>
                            </div>
                            <div class="meta-block">
                                <span class="meta-label">Metode</span>
                                <span class="meta-value">{{ $paymentMethod }}</span>
                            </div>
                            <div class="meta-block meta-block--deadline">
                                <span class="meta-label">Bayar Sebelum</span>
                                <span class="meta-value" data-deadline="{{ optional($order->expires_at)->format('d M Y, H:i') }}" data-countdown="{{ $remainingSeconds ?? '' }}">
                                    @if($order->expires_at)
                                        {{ $order->expires_at->format('d M Y, H:i') }} WIB
                                    @else
                                        Tidak ada batas waktu
                                    @endif
                                </span>
                                @if($order->expires_at)
                                    <span class="countdown" data-countdown-label>
                                        @if($isPaid)
                                            Pembayaran telah dikonfirmasi
                                        @elseif($isExpired)
                                            Waktu pembayaran berakhir
                                        @else
                                            Sisa waktu: <span data-countdown-text>—</span>
                                        @endif
                                    </span>
                                @endif
                            </div>
                        </div>

                        <article class="payment-summary">
                            <h2>Rincian Pesanan</h2>
                            <dl class="summary-list">
                                <div class="summary-item">
                                    <dt>Nomor Pesanan</dt>
                                    <dd>{{ $order->order_number }}</dd>
                                </div>
                                <div class="summary-item">
                                    <dt>Status Pesanan</dt>
                                    <dd data-order-status>{{ ucfirst($order->status) }}</dd>
                                </div>
                                <div class="summary-item">
                                    <dt>Status Pembayaran</dt>
                                    <dd data-order-payment>{{ $isPaid ? 'Paid' : 'Unpaid' }}</dd>
                                </div>
                                @if($order->expires_at)
                                    <div class="summary-item">
                                        <dt>Batas Pembayaran</dt>
                                        <dd>{{ $order->expires_at->translatedFormat('d F Y, H:i') }} WIB</dd>
                                    </div>
                                @endif
                            </dl>
                        </article>

                        <div class="payment-actions">
                            <button type="button" class="btn btn-outline-secondary" data-action="instructions" data-bs-toggle="modal" data-bs-target="#instructionsModal">
                                <i class="bi bi-journal-text me-2"></i>Cara Bayar
                            </button>
                            <button type="button" class="btn btn-success" data-action="pay" {{ ($isPaid || $isExpired) ? 'disabled' : '' }}>
                                <span class="spinner-border spinner-border-sm me-2 d-none" role="status" aria-hidden="true"></span>
                                <i class="bi bi-credit-card me-2"></i>Bayar Sekarang
                            </button>
                            <button type="button" class="btn btn-outline-primary" data-action="refresh">
                                <i class="bi bi-arrow-repeat me-2"></i>Cek Status
                            </button>
                        </div>

                        <div class="payment-feedback" data-feedback aria-live="polite"></div>

                        <footer class="payment-footer">
                            <p><i class="bi bi-shield-check me-2"></i>Pembayaran kamu diproses dengan sistem terenkripsi. Detail kartu atau dompet digital tidak kami simpan.</p>
                            <p><i class="bi bi-info-circle me-2"></i>Pesanan otomatis diteruskan ke tim fulfilment setelah pembayaran dikonfirmasi.</p>
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
                    <h5 class="modal-title" id="instructionsModalLabel">Panduan Pembayaran</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <div class="modal-body">
                    <ol class="instruction-list">
                        <li>Pilih metode pembayaran favoritmu saat checkout.</li>
                        <li>Ikuti instruksi pada aplikasi atau layanan bank untuk menyelesaikan transaksi.</li>
                        <li>Klik <strong>Bayar Sekarang</strong> di halaman ini untuk mengonfirmasi pembayaran.</li>
                        <li>Status akan berubah menjadi <strong>Sudah Dibayar</strong> secara otomatis.</li>
                    </ol>
                    <p class="text-muted small mb-0">Jika mengalami kendala, hubungi layanan pelanggan kami untuk bantuan manual.</p>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-primary" data-bs-dismiss="modal">Mengerti</button>
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

            const setPaidState = (message = 'Pembayaran berhasil dikonfirmasi.', statusValue = 'processing') => {
                statusPill?.classList.remove('is-unpaid');
                statusPill?.classList.add('is-paid');
                if (statusText) statusText.textContent = 'Sudah Dibayar';
                if (orderStatus && statusValue) {
                    const formatted = statusValue.charAt(0).toUpperCase() + statusValue.slice(1);
                    orderStatus.textContent = formatted;
                }
                if (paymentStatus) paymentStatus.textContent = 'Paid';
                if (payButton) payButton.disabled = true;
                if (spinner) spinner.classList.add('d-none');
                if (countdownLabel) {
                    countdownLabel.textContent = 'Pembayaran telah dikonfirmasi';
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
                    showFeedback('Memproses pembayaran...', 'info');

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
                            throw new Error(payload.message || 'Gagal mengonfirmasi pembayaran.');
                        }

                        setPaidState(payload.message, payload.status);
                    } catch (error) {
                        handleError(error.message || 'Terjadi kesalahan tak terduga.');
                    }
                });
            }

            if (refreshButton) {
                refreshButton.addEventListener('click', async () => {
                    showFeedback('Memeriksa status terbaru...', 'info');
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
                            setPaidState('Pembayaran telah dikonfirmasi.', payload.status);
                        } else {
                            showFeedback('Pembayaran belum diterima, silakan selesaikan sebelum batas waktu berakhir.', 'warning');
                        }
                    } catch (error) {
                        handleError('Tidak dapat mengambil status terbaru.');
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
                                countdownLabel.textContent = 'Waktu pembayaran berakhir';
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
                showFeedback('Batas waktu pembayaran telah berakhir. Silakan buat pesanan baru.', 'danger');
            }

            if (@json($isPaid)) {
                showFeedback('Pembayaran telah dikonfirmasi. Terima kasih!', 'success');
            }
        })();
    </script>
</body>
</html>