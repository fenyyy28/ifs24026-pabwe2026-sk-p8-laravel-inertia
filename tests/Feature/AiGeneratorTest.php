<?php

namespace Tests\Feature;

use App\Features\Ai\Contracts\AiClientInterface;
use App\Features\Ai\Services\OpenAiPostGeneratorService;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Http\Client\ConnectionException;
use Illuminate\Support\Facades\Http;
use Tests\TestCase;

class AiGeneratorTest extends TestCase
{
    use RefreshDatabase;

    private function fakeOpenAi(array $articles, int $status = 200): void
    {
        Http::fake(['api.openai.com/*' => Http::response([
            'choices' => [['message' => ['content' => json_encode(['articles' => $articles])]]],
        ], $status)]);
    }

    public function test_generate_returns_items_in_flash(): void
    {
        $this->fakeOpenAi([
            ['title' => 'A', 'category' => 'Teknologi', 'excerpt' => 'e', 'content' => 'c'],
            ['title' => '', 'content' => 'dibuang'],
        ]);

        $this->actingAs(User::factory()->create())
            ->post('/dashboard/posts/ai-generate', ['topic' => 'Laravel', 'count' => 2])
            ->assertSessionHas('ai_items', [['title' => 'A', 'category' => 'Teknologi', 'excerpt' => 'e', 'content' => 'c']]);
    }

    public function test_generate_validates_count_limit(): void
    {
        $this->actingAs(User::factory()->create())
            ->post('/dashboard/posts/ai-generate', ['topic' => 'x', 'count' => 99])
            ->assertSessionHasErrors('count');
    }

    public function test_generate_reports_http_failure(): void
    {
        $this->fakeOpenAi([], 500);

        $this->actingAs(User::factory()->create())
            ->post('/dashboard/posts/ai-generate', ['topic' => 'x', 'count' => 1])
            ->assertSessionHas('error');
    }

    public function test_service_rejects_invalid_json_format(): void
    {
        Http::fake(['*' => Http::response(['choices' => [['message' => ['content' => 'bukan json']]]])]);

        $this->expectExceptionMessage('Format respons AI tidak valid.');
        app(OpenAiPostGeneratorService::class)->generate('x', 1);
    }

    public function test_service_rejects_when_no_valid_articles(): void
    {
        $this->fakeOpenAi([['title' => '', 'content' => '']]);

        $this->expectExceptionMessage('AI tidak menghasilkan artikel yang valid.');
        app(OpenAiPostGeneratorService::class)->generate('x', 1);
    }

    public function test_service_requires_api_key(): void
    {
        config(['openai.api_key' => null]);

        $this->expectExceptionMessage('OPENAI_API_KEY belum dikonfigurasi.');
        app(OpenAiPostGeneratorService::class)->generate('x', 1);
    }

    public function test_service_wraps_connection_errors(): void
    {
        Http::fake(fn () => throw new ConnectionException('timeout'));

        $this->expectExceptionMessage('Tidak dapat menghubungi layanan OpenAI.');
        app(OpenAiPostGeneratorService::class)->generate('x', 1);
    }

    public function test_save_creates_draft_posts(): void
    {
        $user = User::factory()->create();

        $this->actingAs($user)->post('/dashboard/posts/ai-save', ['items' => [
            ['title' => 'AI 1', 'category' => 'Opini', 'excerpt' => 'e', 'content' => 'c'],
            ['title' => 'AI 2', 'content' => 'c2'],
        ]])->assertSessionHas('success');

        $this->assertSame(2, $user->posts()->where('is_published', false)->count());
        $this->actingAs($user)->post('/dashboard/posts/ai-save', ['items' => []])->assertSessionHasErrors('items');
    }

    public function test_interface_is_bound_to_openai_service(): void
    {
        $this->assertInstanceOf(OpenAiPostGeneratorService::class, app(AiClientInterface::class));
    }
}