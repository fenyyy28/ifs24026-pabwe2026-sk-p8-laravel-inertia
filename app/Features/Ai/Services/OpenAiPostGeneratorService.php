<?php

namespace App\Features\Ai\Services;

use App\Features\Ai\Contracts\AiClientInterface;
use App\Features\Ai\DTO\GeneratedPostItem;
use App\Features\Post\Enums\PostCategory;
use Illuminate\Support\Facades\Http;
use Throwable;

class OpenAiPostGeneratorService implements AiClientInterface
{
    public function generate(string $topic, int $count): array
    {
        $apiKey = config('openai.api_key');
        if (! $apiKey) {
            throw new \RuntimeException('OPENAI_API_KEY belum dikonfigurasi.');
        }

        try {
            $response = Http::withToken($apiKey)
                ->timeout((int) config('openai.timeout'))
                ->post(rtrim((string) config('openai.base_url'), '/').'/chat/completions', [
                    'model' => config('openai.model'),
                    'response_format' => ['type' => 'json_object'],
                    'messages' => [
                        ['role' => 'system', 'content' => $this->systemPrompt()],
                        ['role' => 'user', 'content' => "Topik: {$topic}\nJumlah artikel: {$count}"],
                    ],
                ]);
        } catch (Throwable $e) {
            throw new \RuntimeException('Tidak dapat menghubungi layanan OpenAI.', 0, $e);
        }

        if ($response->failed()) {
            throw new \RuntimeException('Layanan OpenAI mengembalikan kesalahan ('.$response->status().').');
        }

        $decoded = json_decode((string) $response->json('choices.0.message.content'), true);
        $articles = is_array($decoded) ? ($decoded['articles'] ?? null) : null;

        if (! is_array($articles)) {
            throw new \RuntimeException('Format respons AI tidak valid.');
        }

        $items = array_values(array_filter(
            array_map(fn ($a) => GeneratedPostItem::fromArray((array) $a), $articles),
            fn (GeneratedPostItem $i) => $i->isValid()
        ));

        if ($items === []) {
            throw new \RuntimeException('AI tidak menghasilkan artikel yang valid.');
        }

        return array_slice($items, 0, $count);
    }

    private function systemPrompt(): string
    {
        $categories = implode(', ', PostCategory::values());

        return <<<PROMPT
        Anda adalah penulis blog profesional berbahasa Indonesia.
        Balas HANYA dengan JSON: {"articles":[{"title":"...","category":"...","excerpt":"1-2 kalimat","content":"artikel Markdown"}]}.
        Nilai "category" harus salah satu dari: {$categories}.
        PROMPT;
    }
}
