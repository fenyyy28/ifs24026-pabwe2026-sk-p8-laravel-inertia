<?php

namespace Tests\Feature;

use App\Features\Post\Models\Post;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Inertia\Testing\AssertableInertia as Assert;
use Tests\TestCase;

class PublicPostTest extends TestCase
{
    use RefreshDatabase;

    public function test_guest_sees_only_published_posts_on_home_and_articles(): void
    {
        Post::factory()->published()->count(2)->create();
        Post::factory()->create();

        foreach (['/', '/articles'] as $url) {
            $this->get($url)->assertOk()->assertInertia(fn (Assert $p) => $p
                ->component('Public/PostIndex')->has('posts.data', 2));
        }
    }

    public function test_search_and_category_filters(): void
    {
        Post::factory()->published()->create(['title' => 'Belajar Laravel', 'category' => 'Teknologi']);
        Post::factory()->published()->create(['title' => 'Resep Kopi', 'category' => 'Gaya Hidup']);

        $this->get('/articles?search=laravel')->assertInertia(fn (Assert $p) => $p->has('posts.data', 1));
        $this->get('/articles?category=Gaya+Hidup')->assertInertia(fn (Assert $p) => $p->has('posts.data', 1));
        $this->get('/articles?category=Asal')->assertInertia(fn (Assert $p) => $p->has('posts.data', 2));
    }

    public function test_show_published_post_by_slug(): void
    {
        $post = Post::factory()->published()->create();

        $this->get("/articles/{$post->slug}")->assertOk()->assertInertia(fn (Assert $p) => $p
            ->component('Public/PostShow')->where('post.title', $post->title));
    }

    public function test_draft_post_returns_404(): void
    {
        $post = Post::factory()->create();

        $this->get("/articles/{$post->slug}")->assertNotFound();
    }
}
