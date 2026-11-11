<?php

namespace Tests\Unit;

use App\Features\Ai\DTO\GeneratedPostItem;
use PHPUnit\Framework\TestCase;

class GeneratedPostItemTest extends TestCase
{
    public function test_from_array_normalizes_values(): void
    {
        $item = GeneratedPostItem::fromArray(['title' => ' Halo ', 'category' => 'xx', 'content' => ' Isi ']);

        $this->assertTrue($item->isValid());
        $this->assertSame(['title' => 'Halo', 'category' => 'Umum', 'excerpt' => '', 'content' => 'Isi'], $item->toArray());
    }

    public function test_item_without_title_or_content_is_invalid(): void
    {
        $this->assertFalse(GeneratedPostItem::fromArray(['content' => 'x'])->isValid());
        $this->assertFalse(GeneratedPostItem::fromArray(['title' => 'x'])->isValid());
    }
}
