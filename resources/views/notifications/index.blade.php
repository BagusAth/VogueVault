<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.10.0/font/bootstrap-icons.css">
    <link rel="stylesheet" href="{{ asset('css/product.css') }}">
</head>
<body>
    @include('partials.navbar')
    <div class="container">
        <h2 class="text-xl font-bold mb-4">Notifikasi</h2>

        @if(empty($notifications) || count($notifications) === 0)
            <p>class="text-muted">
                <i class="bi bi-bell text-warning"></i> Tidak ada notifikasi baru>
            </p>
        @else
            <table class="table table-borderless">
                <tbody>
                    @foreach($notifications as $notif)
                        <tr class="border-b">
                            <td class="p-2 text-center">
                                <i class="bi bi-bell text-warning"></i>
                            </td>
                            <td class="p-2">{{ $notif['message'] }}</td>
                        </tr>
                    @endforeach
                </tbody>
            </table>
        @endif
    </div>
</body>
</html>
