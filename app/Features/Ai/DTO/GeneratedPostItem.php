<?php

namespace App\Features\Ai\DTO;

use App\Features\Post\Enums\PostCategory;

final readonly class GeneratedPostItem
{
    public function __construct(
        public string $title,
        public string $category,
        public string $excerpt,
        public string $content,
    ) {}

    public static function fromArray(array $data): self
    {
        return new self(
            title: trim((string) ($data['title'] ?? '')),
            category: PostCategory::fromLabel($data['category'] ?? null)->value,
            excerpt: trim((string) ($data['excerpt'] ?? '')),
            content: trim((string) ($data['content'] ?? '')),
        );
    }

    public function isValid(): bool
    {
        return $this->title !== '' && $this->content !== '';
    }

    /** @return array{title: string, category: string, excerpt: string, content: string} */
    public function toArray(): array
    {
        return [
            'title' => $this->title,
            'category' => $this->category,
            'excerpt' => $this->excerpt,
            'content' => $this->content,
        ];
    }
}
