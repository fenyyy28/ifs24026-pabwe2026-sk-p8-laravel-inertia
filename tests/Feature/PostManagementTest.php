<?php

namespace Tests\Feature;

use App\Features\Post\Models\Post;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\Storage;
use Inertia\Testing\AssertableInertia as Assert;
use Tests\TestCase;

class PostManagementTest extends TestCase
{
    use RefreshDatabase;

    private User $user;

    protected function setUp(): void
    {
        parent::setUp();
        Storage::fake('public');
        $this->user = User::factory()->create();
    }

    public function test_index_lists_own_posts_with_stats_and_filters(): void
    {
        Post::factory()->published()->for($this->user)->count(2)->create(['title' => 'Alpha satu']);
        Post::factory()->for($this->user)->create(['title' => 'Beta dua']);
        Post::factory()->create();

        $this->actingAs($this->user)->get('/dashboard/posts')->assertInertia(fn (Assert $p) => $p
            ->component('Post/Index')->has('posts.data', 3)
            ->where('stats', ['total' => 3, 'published' => 2, 'draft' => 1]));

        $this->actingAs($this->user)->get('/dashboard/posts?status=draft')->assertInertia(fn (Assert $p) => $p->has('posts.data', 1));
        $this->actingAs($this->user)->get('/dashboard/posts?status=published&search=alpha')->assertInertia(fn (Assert $p) => $p->has('posts.data', 2));
    }

    public function test_store_creates_post_with_slug_excerpt_and_cover(): void
    {
        $this->actingAs($this->user)->post('/dashboard/posts', [
            'title' => 'Judul Baru', 'category' => 'Teknologi', 'content' => "# Halo\n\nIsi **artikel**.",
            'is_published' => true, 'cover' => UploadedFile::fake()->image('c.jpg'),
        ])->assertRedirect()->assertSessionHas('success');

        $post = Post::firstOrFail();
        $this->assertSame('judul-baru', $post->slug);
        $this->assertTrue($post->is_published);
        $this->assertNotNull($post->published_at);
        $this->assertNotSame('', $post->excerpt);
        Storage::disk('public')->assertExists($post->cover_image_path);
        $this->assertStringContainsString('/storage/covers/', $post->cover_url);
    }

    public function test_store_generates_unique_slug_for_duplicate_titles(): void
    {
        $data = ['title' => 'Sama', 'category' => 'Umum', 'content' => 'x'];
        $this->actingAs($this->user)->post('/dashboard/posts', $data);
        $this->actingAs($this->user)->post('/dashboard/posts', $data);

        $this->assertSame(2, Post::query()->distinct('slug')->count('slug'));
    }

    public function test_store_validation(): void
    {
        $this->actingAs($this->user)->post('/dashboard/posts', ['category' => 'Aneh'])
            ->assertSessionHasErrors(['title', 'category', 'content']);
    }

    public function test_show_and_update_own_post(): void
    {
        $post = Post::factory()->for($this->user)->create();

        $this->actingAs($this->user)->get("/dashboard/posts/{$post->id}")
            ->assertInertia(fn (Assert $p) => $p->component('Post/Show')->where('post.id', $post->id));

        $this->actingAs($this->user)->put("/dashboard/posts/{$post->id}", [
            'title' => 'Diubah', 'category' => 'Opini', 'content' => 'Konten baru', 'excerpt' => 'Ringkas', 'is_published' => true,
            'cover' => UploadedFile::fake()->image('a.jpg'),
        ])->assertSessionHas('success');

        $post->refresh();
        $this->assertSame('Diubah', $post->title);
        $this->assertSame('Opini', $post->category->value);
        $this->assertTrue($post->is_published);
        $this->assertNotNull($post->cover_image_path);

        // unpublish via update clears published_at, via multipart route
        $this->actingAs($this->user)->post("/dashboard/posts/{$post->id}", [
            '_method' => 'put', 'title' => 'Diubah', 'category' => 'Opini', 'content' => 'Konten baru', 'is_published' => false,
        ]);
        $this->assertNull($post->refresh()->published_at);
    }

    public function test_cannot_access_others_posts(): void
    {
        $other = Post::factory()->create();

        $this->actingAs($this->user)->get("/dashboard/posts/{$other->id}")->assertForbidden();
        $this->actingAs($this->user)->put("/dashboard/posts/{$other->id}", ['title' => 'x', 'category' => 'Umum', 'content' => 'x'])->assertForbidden();
        $this->actingAs($this->user)->delete("/dashboard/posts/{$other->id}")->assertForbidden();
        $this->assertDatabaseHas('posts', ['id' => $other->id]);
    }

    public function test_destroy_removes_post_and_cover(): void
    {
        $post = Post::factory()->for($this->user)->create();
        $this->actingAs($this->user)->post("/dashboard/posts/{$post->id}/cover", ['cover' => UploadedFile::fake()->image('c.jpg')]);
        $path = $post->refresh()->cover_image_path;

        $this->actingAs($this->user)->delete("/dashboard/posts/{$post->id}")->assertRedirect('/dashboard/posts');

        $this->assertModelMissing($post);
        Storage::disk('public')->assertMissing($path);
    }

    public function test_toggle_publish(): void
    {
        $post = Post::factory()->for($this->user)->create();

        $this->actingAs($this->user)->patch("/dashboard/posts/{$post->id}/publish");
        $this->assertTrue($post->refresh()->is_published);
        $this->assertNotNull($post->published_at);

        $this->actingAs($this->user)->patch("/dashboard/posts/{$post->id}/publish");
        $this->assertFalse($post->refresh()->is_published);
        $this->assertNull($post->published_at);
    }

    public function test_cover_upload_replace_and_remove(): void
    {
        $post = Post::factory()->for($this->user)->create();

        $this->actingAs($this->user)->post("/dashboard/posts/{$post->id}/cover", ['cover' => UploadedFile::fake()->image('1.jpg')]);
        $first = $post->refresh()->cover_image_path;
        $this->actingAs($this->user)->post("/dashboard/posts/{$post->id}/cover", ['cover' => UploadedFile::fake()->image('2.jpg')]);
        $second = $post->refresh()->cover_image_path;

        Storage::disk('public')->assertMissing($first);
        Storage::disk('public')->assertExists($second);

        $this->actingAs($this->user)->delete("/dashboard/posts/{$post->id}/cover");
        $this->assertNull($post->refresh()->cover_image_path);
        Storage::disk('public')->assertMissing($second);

        $this->actingAs($this->user)->post("/dashboard/posts/{$post->id}/cover", [])->assertSessionHasErrors('cover');
    }

    public function test_removing_cover_without_file_is_safe(): void
    {
        $post = Post::factory()->for($this->user)->create();

        $this->actingAs($this->user)->delete("/dashboard/posts/{$post->id}/cover")->assertRedirect();
    }
}
