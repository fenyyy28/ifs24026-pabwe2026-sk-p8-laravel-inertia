<?php

namespace App\Features\Post\Services;

use App\Features\Post\Enums\PostCategory;
use App\Models\User;
use Illuminate\Support\Collection;
use PhpOffice\PhpSpreadsheet\IOFactory;
use PhpOffice\PhpSpreadsheet\Spreadsheet;
use PhpOffice\PhpSpreadsheet\Writer\Xlsx;

class PostExcelService
{
    public const HEADERS = ['Judul', 'Kategori', 'Ringkasan', 'Konten', 'Status'];

    public function __construct(private readonly PostService $posts) {}

    /** Hasilkan isi berkas .xlsx (binary string). */
    public function export(Collection $posts, bool $withAuthor = false): string
    {
        $spreadsheet = new Spreadsheet;
        $sheet = $spreadsheet->getActiveSheet();
        $sheet->setTitle('Artikel');

        $headers = $withAuthor ? [...self::HEADERS, 'Tanggal Publikasi', 'Penulis'] : self::HEADERS;
        $sheet->fromArray($headers, null, 'A1');

        $row = 2;
        foreach ($posts as $post) {
            $values = [
                $post->title,
                $post->category->value,
                (string) $post->excerpt,
                $post->content,
                $post->is_published ? 'Published' : 'Draft',
            ];
            if ($withAuthor) {
                $values[] = $post->published_at?->format('Y-m-d H:i') ?? '';
                $values[] = $post->user?->name ?? '';
            }
            $sheet->fromArray($values, null, 'A'.$row);
            $row++;
        }

        foreach (range('A', chr(ord('A') + count($headers) - 1)) as $col) {
            $sheet->getColumnDimension($col)->setAutoSize(true);
        }
        $sheet->getStyle('A1:'.chr(ord('A') + count($headers) - 1).'1')->getFont()->setBold(true);

        $path = tempnam(sys_get_temp_dir(), 'posts_');
        (new Xlsx($spreadsheet))->save($path);
        $binary = (string) file_get_contents($path);
        @unlink($path);

        return $binary;
    }

    /** Impor artikel dari berkas Excel; mengembalikan jumlah artikel yang dibuat. */
    public function import(User $user, string $path): int
    {
        $rows = IOFactory::load($path)->getActiveSheet()->toArray(null, true, true, false);
        array_shift($rows);

        $items = [];
        foreach ($rows as $row) {
            $title = trim((string) ($row[0] ?? ''));
            $content = trim((string) ($row[3] ?? ''));

            if ($title === '' || $content === '') {
                continue;
            }

            $items[] = [
                'title' => $title,
                'category' => PostCategory::fromLabel($row[1] ?? null)->value,
                'excerpt' => (string) ($row[2] ?? ''),
                'content' => $content,
                'is_published' => strtolower(trim((string) ($row[4] ?? ''))) === 'published',
            ];
        }

        return $this->posts->createMany($user, $items);
    }
}
