<?php

namespace App\Features\Post\Contracts;

use App\Features\Post\Models\Post;
use App\Models\User;
use Illuminate\Contracts\Pagination\LengthAwarePaginator;
use Illuminate\Support\Collection;

interface PostRepositoryInterface
{
    /** @param array{search?: ?string, category?: ?string, status?: ?string} $filters */
    public function paginateForUser(User $user, array $filters = [], int $perPage = 10): LengthAwarePaginator;

    public function paginatePublished(array $filters = [], int $perPage = 9): LengthAwarePaginator;

    public function paginateAll(array $filters = [], int $perPage = 10): LengthAwarePaginator;

    public function findPublishedBySlug(string $slug): ?Post;

    public function slugExists(string $slug): bool;

    public function create(array $data): Post;

    public function update(Post $post, array $data): Post;

    public function delete(Post $post): void;

    /** @return array{total: int, published: int, draft: int} */
    public function countsForUser(User $user): array;

    /** @return Collection<int, Post> */
    public function allForUser(User $user): Collection;

    /** @return Collection<int, Post> */
    public function all(): Collection;
}
