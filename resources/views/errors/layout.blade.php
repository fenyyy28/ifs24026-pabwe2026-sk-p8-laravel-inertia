@php
    $code = isset($exception) && method_exists($exception, 'getStatusCode') ? $exception->getStatusCode() : 500;
    $titles = [
        401 => 'Perlu login',
        402 => 'Pembayaran diperlukan',
        403 => 'Akses ditolak',
        404 => 'Halaman tidak ditemukan',
        419 => 'Sesi kedaluwarsa',
        429 => 'Terlalu banyak permintaan',
        500 => 'Terjadi kesalahan server',
        503 => 'Layanan sedang tidak tersedia',
    ];
    $title = $titles[$code] ?? 'Terjadi kesalahan';
@endphp
<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta name="description" content="Halaman kesalahan BlogVerse Pro: {{ $title }}.">
    <title>{{ $code }} · {{ $title }} · BlogVerse Pro</title>
    <style>
        body { margin: 0; background: #f8fafc; color: #1e293b; font: 16px/1.6 Georgia, "Times New Roman", serif; }
        main { max-width: 36rem; margin: 0 auto; padding: 6rem 1.5rem; text-align: center; }
        h1 { font-size: 2rem; margin: 0 0 1rem; color: #312e81; }
        p { margin: 0 0 1.5rem; }
        a { display: inline-block; padding: .6rem 1.2rem; border-radius: .5rem; background: #3730a3; color: #ffffff; text-decoration: none; }
    </style>
</head>
<body>
    <main>
        <h1>{{ $code }} — {{ $title }}</h1>
        <p>Maaf, permintaan Anda tidak dapat diproses.</p>
        <a href="/">Kembali ke beranda</a>
    </main>
</body>
</html>