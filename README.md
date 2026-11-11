# BlogVerse Pro — ifs24026-pabwe2026-sk-p8-laravel-inertia

Studi Kasus PABWE 2026 P8: platform blog multi-tier (Publik · Penulis · Admin) dengan
Laravel 13, Inertia.js, React, PostgreSQL, PhpSpreadsheet, dan integrasi OpenAI.
Arsitektur: Clean Architecture & Feature-First (`app/Features/*`).

## Menjalankan

```bash
composer install
cp .env.example .env && php artisan key:generate
psql -U postgres -c "CREATE DATABASE pabwe_p8_blog;"
php artisan migrate --seed
php artisan storage:link
npm install && npm run build      # atau: npm run dev
php artisan serve
```

Akun seeder: `admin / admin` (Admin) dan `member / member` (Penulis).
Isi `OPENAI_API_KEY` di `.env` untuk fitur AI Generator.

## Pengujian

```bash
php artisan test
composer test-coverage     # butuh pcov/xdebug
composer audit
```

Test memakai SQLite in-memory (lihat `phpunit.xml`), sehingga tidak butuh PostgreSQL.

## Rute

| Tingkat | URL |
|---|---|
| Publik | `/`, `/articles`, `/articles/{slug}`, `/login`, `/register` |
| Penulis | `/dashboard/posts`, `/dashboard/posts/{post}`, export/import, AI generate/save, `/profile` |
| Admin | `/admin/users`, `/admin/users/{user}/posts`, `/admin/posts` (+ export Excel) |
