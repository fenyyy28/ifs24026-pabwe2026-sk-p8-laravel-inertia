<?php

namespace App\Features\Post\Repositories;

use App\Features\Post\Contracts\PostRepositoryInterface;
use App\Features\Post\Enums\PostCategory;
use App\Features\Post\Models\Post;
use App\Models\User;
use Illuminate\Contracts\Pagination\LengthAwarePaginator;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Support\Collection;

class PostRepository implements PostRepositoryInterface
{
    public function paginateForUser(User $user, array $filters = [], int $perPage = 10): LengthAwarePaginator
    {
        return $this->filtered(Post::query()->where('user_id', $user->id), $filters, true)
            ->latest()->latest('id')
            ->paginate($perPage)->withQueryString();
    }

    public function paginatePublished(array $filters = [], int $perPage = 9): LengthAwarePaginator
    {
        return $this->filtered(Post::query()->with('user:id,name,username')->where('is_published', true), $filters, false)
            ->orderByDesc('published_at')->orderByDesc('id')
            ->paginate($perPage)->withQueryString();
    }

    public function paginateAll(array $filters = [], int $perPage = 10): LengthAwarePaginator
    {
        return $this->filtered(Post::query()->with('user:id,name,username'), $filters, true)
            ->latest()->latest('id')
            ->paginate($perPage)->withQueryString();
    }

    public function findPublishedBySlug(string $slug): ?Post
    {
        return Post::query()->with('user:id,name,username')
            ->where('is_published', true)->where('slug', $slug)->first();
    }

    public function slugExists(string $slug): bool
    {
        return Post::query()->where('slug', $slug)->exists();
    }

    public function create(array $data): Post
    {
        return Post::create($data);
    }

    public function update(Post $post, array $data): Post
    {
        $post->update($data);

        return $post;
    }

    public function delete(Post $post): void
    {
        $post->delete();
    }

    public function countsForUser(User $user): array
    {
        $total = Post::query()->where('user_id', $user->id)->count();
        $published = Post::query()->where('user_id', $user->id)->where('is_published', true)->count();

        return ['total' => $total, 'published' => $published, 'draft' => $total - $published];
    }

    public function allForUser(User $user): Collection
    {
        return Post::query()->where('user_id', $user->id)->orderBy('id')->get();
    }

    public function all(): Collection
    {
        return Post::query()->with('user:id,name,username')->orderBy('id')->get();
    }

    private function filtered(Builder $query, array $filters, bool $withStatus): Builder
    {
        $search = trim((string) ($filters['search'] ?? ''));
        if ($search !== '') {
            $like = '%'.mb_strtolower($search).'%';
            $query->where(fn (Builder $q) => $q
                ->whereRaw('LOWER(title) LIKE ?', [$like])
                ->orWhereRaw('LOWER(content) LIKE ?', [$like]));
        }

        $category = $filters['category'] ?? null;
        if ($category && in_array($category, PostCategory::values(), true)) {
            $query->where('category', $category);
        }

        $status = $filters['status'] ?? null;
        if ($withStatus && in_array($status, ['published', 'draft'], true)) {
            $query->where('is_published', $status === 'published');
        }

        return $query;
    }
}
