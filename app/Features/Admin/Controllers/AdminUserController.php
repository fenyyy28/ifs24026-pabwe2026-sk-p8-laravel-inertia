<?php

namespace App\Features\Admin\Controllers;

use App\Features\Admin\Services\AdminUserService;
use App\Models\User;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Validation\Rule;
use Inertia\Inertia;
use Inertia\Response;

class AdminUserController
{
    public function __construct(private readonly AdminUserService $users) {}

    public function index(Request $request): Response
    {
        return Inertia::render('Admin/UserList', [
            'users' => $this->users->paginate($request->query('search')),
            'filters' => ['search' => (string) $request->query('search', '')],
        ]);
    }

    public function updateRole(Request $request, User $user): RedirectResponse
    {
        $data = $request->validate(['role' => ['required', Rule::in([User::ROLE_ADMIN, User::ROLE_MEMBER])]]);

        if ($user->is($request->user())) {
            return back()->with('error', 'Anda tidak dapat mengubah peran akun sendiri.');
        }

        $this->users->changeRole($user, $data['role']);

        return back()->with('success', 'Peran pengguna diperbarui.');
    }

    public function destroy(Request $request, User $user): RedirectResponse
    {
        if ($user->is($request->user())) {
            return back()->with('error', 'Anda tidak dapat menghapus akun sendiri.');
        }

        $this->users->delete($user);

        return back()->with('success', 'Pengguna berhasil dihapus.');
    }
}
