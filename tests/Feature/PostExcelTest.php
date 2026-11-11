<?php

namespace Tests\Feature;

use App\Features\Post\Models\Post;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Http\UploadedFile;
use PhpOffice\PhpSpreadsheet\IOFactory;
use PhpOffice\PhpSpreadsheet\Spreadsheet;
use PhpOffice\PhpSpreadsheet\Writer\Xlsx;
use Tests\TestCase;

class PostExcelTest extends TestCase
{
    use RefreshDatabase;

    private function xlsx(array $rows): UploadedFile
    {
        $sheet = new Spreadsheet;
        $sheet->getActiveSheet()->fromArray($rows, null, 'A1');
        $path = tempnam(sys_get_temp_dir(), 'imp').'.xlsx';
        (new Xlsx($sheet))->save($path);

        return new UploadedFile($path, 'artikel.xlsx', null, null, true);
    }

    public function test_member_exports_only_own_posts(): void
    {
        $user = User::factory()->create(['username' => 'penulis']);
        Post::factory()->published()->for($user)->create(['title' => 'Milik Saya']);
        Post::factory()->create(['title' => 'Milik Orang']);

        $response = $this->actingAs($user)->get('/dashboard/posts/export');
        $response->assertOk()->assertDownload('artikel-penulis.xlsx');

        $path = tempnam(sys_get_temp_dir(), 'exp').'.xlsx';
        file_put_contents($path, $response->streamedContent());
        $rows = IOFactory::load($path)->getActiveSheet()->toArray();

        $this->assertCount(2, $rows);
        $this->assertSame('Milik Saya', $rows[1][0]);
        $this->assertSame('Published', $rows[1][4]);
    }

    public function test_import_creates_valid_rows_and_skips_empty(): void
    {
        $user = User::factory()->create();
        $file = $this->xlsx([
            ['Judul', 'Kategori', 'Ringkasan', 'Konten', 'Status'],
            ['Satu', 'Bisnis', 'R1', 'Isi satu', 'Published'],
            ['Dua', 'Tidak Ada', '', 'Isi dua', 'Draft'],
            ['', '', '', 'Tanpa judul', ''],
            ['Tiga', 'Umum', '', '', ''],
        ]);

        $this->actingAs($user)->post('/dashboard/posts/import', ['file' => $file])
            ->assertSessionHas('success', '2 artikel berhasil diimpor.');

        $this->assertSame(2, $user->posts()->count());
        $this->assertTrue($user->posts()->where('title', 'Satu')->firstOrFail()->is_published);
        $this->assertSame('Umum', $user->posts()->where('title', 'Dua')->firstOrFail()->category->value);
    }

    public function test_import_requires_excel_file(): void
    {
        $this->actingAs(User::factory()->create())
            ->post('/dashboard/posts/import', ['file' => UploadedFile::fake()->create('x.pdf', 10, 'application/pdf')])
            ->assertSessionHasErrors('file');
    }
}
