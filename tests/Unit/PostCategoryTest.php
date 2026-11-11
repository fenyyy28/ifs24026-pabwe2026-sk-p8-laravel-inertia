<?php

namespace Tests\Unit;

use App\Features\Post\Enums\PostCategory;
use PHPUnit\Framework\TestCase;

class PostCategoryTest extends TestCase
{
    public function test_values_contains_all_categories(): void
    {
        $this->assertSame(
            ['Teknologi', 'Pendidikan', 'Gaya Hidup', 'Bisnis', 'Opini', 'Umum'],
            PostCategory::values()
        );
    }

    public function test_from_label_falls_back_to_umum(): void
    {
        $this->assertSame(PostCategory::Bisnis, PostCategory::fromLabel(' Bisnis '));
        $this->assertSame(PostCategory::Umum, PostCategory::fromLabel('tidak ada'));
        $this->assertSame(PostCategory::Umum, PostCategory::fromLabel(null));
    }

    public function test_every_case_has_description(): void
    {
        foreach (PostCategory::cases() as $case) {
            $this->assertNotSame('', $case->description());
        }
    }
}
