<?php

namespace App\Features\Post\Services;

use App\Features\Post\Contracts\PostRepositoryInterface;
use App\Features\Post\Enums\PostCategory;
use App\Features\Post\Models\Post;
use App\Models\User;
use Illuminate\Contracts\Pagination\LengthAwarePaginator;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Str;

class PostService
{
    public function __construct(private readonly PostRepositoryInterface $posts) {}

    public function listForUser(User $user, array $filters): LengthAwarePaginator
    {
        return $this->posts->paginateForUser($user, $filters);
    }

    public function statsForUser(User $user): array
    {
        return $this->posts->countsForUser($user);
    }

    public function listPublished(array $filters): LengthAwarePaginator
    {
        return $this->posts->paginatePublished($filters);
    }

    public function listAll(array $filters): LengthAwarePaginator
    {
        return $this->posts->paginateAll($filters);
    }

    public function findPublishedOrFail(string $slug): Post
    {
        return $this->posts->findPublishedBySlug($slug) ?? abort(404);
    }

    public function create(User $user, array $data, ?UploadedFile $cover = null): Post
    {
        $published = (bool) ($data['is_published'] ?? false);

        $post = $this->posts->create([
            'user_id' => $user->id,
            'title' => $data['title'],
            'slug' => $this->uniqueSlug($data['title']),
            'category' => PostCategory::fromLabel($data['category'] ?? null)->value,
            'excerpt' => $this->excerptFor($data),
            'content' => $data['content'],
            'is_published' => $published,
            'published_at' => $published ? now() : null,
        ]);

        return $cover ? $this->setCover($post, $cover) : $post;
    }

    public function update(Post $post, array $data, ?UploadedFile $cover = null): Post
    {
        $published = (bool) ($data['is_published'] ?? $post->is_published);

        $post = $this->posts->update($post, [
            'title' => $data['title'],
            'category' => PostCategory::fromLabel($data['category'] ?? null)->value,
            'excerpt' => $this->excerptFor($data),
            'content' => $data['content'],
            'is_published' => $published,
            'published_at' => $published ? ($post->published_at ?? now()) : null,
        ]);

        return $cover ? $this->setCover($post, $cover) : $post;
    }

    public function delete(Post $post): void
    {
        $this->deleteCoverFile($post);
        $this->posts->delete($post);
    }

    public function togglePublish(Post $post): Post
    {
        $publish = ! $post->is_published;

        return $this->posts->update($post, [
            'is_published' => $publish,
            'published_at' => $publish ? now() : null,
        ]);
    }

    public function setCover(Post $post, UploadedFile $file): Post
    {
        $this->deleteCoverFile($post);

        return $this->posts->update($post, [
            'cover_image_path' => $file->store('covers', 'public'),
        ]);
    }

    public function removeCover(Post $post): Post
    {
        $this->deleteCoverFile($post);

        return $this->posts->update($post, ['cover_image_path' => null]);
    }

    /**
     * Buat banyak artikel sekaligus (impor Excel / simpan batch AI).
     *
     * @param  array<int, array{title: string, category?: ?string, excerpt?: ?string, content: string, is_published?: bool}>  $items
     */
    public function createMany(User $user, array $items): int
    {
        foreach ($items as $item) {
            $this->create($user, $item);
        }

        return count($items);
    }

    public function allForUser(User $user)
    {
        return $this->posts->allForUser($user);
    }

    public function all()
    {
        return $this->posts->all();
    }

    private function excerptFor(array $data): string
    {
        $excerpt = trim((string) ($data['excerpt'] ?? ''));

        return $excerpt !== '' ? $excerpt : Str::limit(trim(strip_tags(Str::markdown($data['content']))), 160);
    }

    private function uniqueSlug(string $title): string
    {
        $base = Str::slug($title) ?: 'artikel';
        $slug = $base;

        while ($this->posts->slugExists($slug)) {
            $slug = $base.'-'.Str::lower(Str::random(5));
        }

        return $slug;
    }

    private function deleteCoverFile(Post $post): void
    {
        if ($post->cover_image_path) {
            Storage::disk('public')->delete($post->cover_image_path);
        }
    }
}
