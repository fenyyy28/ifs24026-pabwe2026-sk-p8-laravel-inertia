<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta name="description" content="BlogVerse Pro adalah platform blog multi-peran: baca artikel secara publik, tulis dan kelola artikelmu sebagai penulis, serta pantau konten sebagai admin.">
    <meta name="theme-color" content="#4338ca">
    <title inertia>{{ config('app.name') }}</title>
    <link rel="icon" type="image/svg+xml" href="/favicon.svg">
    @vite(['resources/css/app.css', 'resources/js/app.jsx'])
    @inertiaHead
</head>
<body class="bg-slate-50 text-slate-800 antialiased">
    <noscript>
        <div style="padding:2rem;font:16px/1.5 Georgia,serif;color:#1e293b">
            <h1 style="margin:0 0 .5rem;font-size:1.5rem">BlogVerse Pro</h1>
            <p style="margin:0">JavaScript diperlukan untuk membuka aplikasi ini.</p>
        </div>
    </noscript>
    @inertia
</body>
</html>
