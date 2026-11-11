<?php

namespace App\Features\Admin\Services;

use App\Models\User;
use Illuminate\Contracts\Pagination\LengthAwarePaginator;

class AdminUserService
{
    public function paginate(?string $search, int $perPage = 10): LengthAwarePaginator
    {
        $search = trim((string) $search);

        return User::query()
            ->withCount(['posts', 'posts as published_posts_count' => fn ($q) => $q->where('is_published', true)])
            ->when($search !== '', function ($q) use ($search) {
                $like = '%'.mb_strtolower($search).'%';
                $q->where(fn ($w) => $w->whereRaw('LOWER(name) LIKE ?', [$like])->orWhereRaw('LOWER(username) LIKE ?', [$like]));
            })
            ->orderBy('id')
            ->paginate($perPage)->withQueryString();
    }

    public function changeRole(User $user, string $role): User
    {
        $user->update(['role' => $role]);

        return $user;
    }

    public function delete(User $user): void
    {
        $user->delete();
    }
}
