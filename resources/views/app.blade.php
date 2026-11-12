<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta name="description" content="BlogVerse Pro adalah platform blog multi-peran: baca artikel secara publik, tulis dan kelola artikelmu sebagai penulis, serta pantau konten sebagai admin.">
    <title inertia>{{ config('app.name') }}</title>
    <link rel="icon" type="image/svg+xml" href="/favicon.svg">
    @viteReactRefresh
    @vite(['resources/css/app.css', 'resources/js/app.jsx'])
    @inertiaHead
</head>
<body class="bg-slate-50 text-slate-800 antialiased">
    <div id="boot" style="padding:2rem;font:16px/1.5 Georgia,serif;color:#1e293b">BlogVerse Pro — memuat halaman…</div>
    @inertia
</body>
</html>
