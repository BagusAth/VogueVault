<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.10.0/font/bootstrap-icons.css">
    <link rel="stylesheet" href="{{ asset('css/navbar.css') }}">
    <link rel="stylesheet" href="{{ asset('css/product.css') }}">
    <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
    <title>Pembayaran</title>
</head>
<body class="bg-light">
    @include('partials.navbar')

    <div class="container my-5">
        <div class="row justify-content-center">
            <div class="col-lg-8 bg-white p-4 rounded shadow-sm">
                <h4 class="fw-bold mb-4">Pembayaran</h4>

                {{-- Batas Waktu Bayar --}}
                <div class="mb-3">
                    <p class="text-muted mb-1"><i class="bi bi-clock"></i> Bayar sebelum:</p>
                    <p class="fw-bold">{{ $order->expires_at->format('d M Y, H:i') }} WIB</p>
                </div>

                {{-- Order ID --}}
                <div class="border rounded p-3 mb-3">
                    <p class="text-muted mb-1">Nomor Pesanan</p>
                    <p class="fs-5 fw-bold">{{ $order->order_number }}</p>
                </div>

                {{-- Total --}}
                <p class="fs-5 fw-semibold mb-3">
                    Total Tagihan: <span class="fw-bold text-success">Rp{{ number_format($order->grand_total,0,',','.') }}</span>
                </p>

                {{-- Tombol --}}
                <div class="d-flex justify-content-between flex-wrap">
                    <button id="caraBayar" class="btn btn-outline-secondary w-100 mb-2" onclick="showInstructions()">Cara Bayar</button>
                    <button id="payNow" class="btn btn-outline-success w-100 mb-2">Bayar Sekarang</button>
                    <button id="checkStatus" class="btn btn-outline-primary w-100 mb-2">Cek Status</button>
                </div>

                {{-- Hasil Status --}}
                <div id="statusResult" class="mt-3 text-muted small"></div>

                {{-- Info tambahan --}}
                <div class="mt-4 small text-muted">
                    <p>• Pembayaran Virtual Account hanya bisa dilakukan dari bank yang kamu pilih.</p>
                    <p>• Pesanan akan diteruskan ke penjual setelah pembayaran diverifikasi.</p>
                </div>
            </div>
        </div>
    </div>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
    <script>
        // Tombol "Cara Bayar"
        document.getElementById('caraBayar').addEventListener('click', function() {
            Swal.fire({
                icon: 'info',
                title: 'Panduan Pembayaran',
                html: `
                    <div style="text-align:left">
                        <p><b>1.</b> Pilih metode pembayaran yang kamu inginkan (VA, QRIS, dll).</p>
                        <p><b>2.</b> Tekan tombol <b>"Bayar Sekarang"</b> untuk menyelesaikan pembayaran.</p>
                        <p><b>3.</b> Setelah pembayaran berhasil, status pesananmu akan berubah menjadi <b>"Paid"</b>.</p>
                    </div>
                `,
                confirmButtonText: 'Mengerti',
                confirmButtonColor: '#3085d6'
            });
        });

        const userName = "{{ Auth::user()->name ?? 'User' }}";
        const orderNumber = "{{ $order->order_number }}";

        // Tombol "Bayar Sekarang"
        document.getElementById('payNow').addEventListener('click', function() {
        Swal.fire({
            icon: 'success',
            title: 'Pembayaran Berhasil!',
            html: `<b>Hai ${userName}</b>, pembayaran kamu untuk pesanan <b>#${orderNumber}</b> telah berhasil`,
            showConfirmButton: true,
            confirmButtonText: 'OK',
            confirmButtonColor: '#28a745'
        }).then(() => {
            // Simpan status ke localStorage
            localStorage.setItem('paymentStatus_' + orderNumber, 'Paid');

            // Update status teks di halaman
            document.getElementById('statusResult').innerText = "Status: Paid";
            }); 
        });

        // Tombol "Cek Status"
        document.getElementById('checkStatus').addEventListener('click', function() {
        const localStatus = localStorage.getItem('paymentStatus_' + orderNumber);

        if (localStatus === 'Paid') {
            Swal.fire({
                icon: 'success',
                title: 'Status Pembayaran',
                text: 'Pembayaran kamu sudah selesai!',
                confirmButtonColor: '#3085d6'
            });
            document.getElementById('statusResult').innerText = "Status: Paid";
        } else {
            Swal.fire({
                icon: 'error',
                title: 'Status Pembayaran',
                text: 'Pembayaran kamu belum dilakukan!',
                confirmButtonColor: '#d33'
            });
            document.getElementById('statusResult').innerText = "Status: Unpaid";
            }
        });
    </script>
</body>
</html>