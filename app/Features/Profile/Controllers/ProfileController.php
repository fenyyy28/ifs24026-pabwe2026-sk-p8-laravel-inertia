<?php

namespace App\Features\Profile\Controllers;

use App\Features\Profile\Services\ProfileService;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Validation\Rule;
use Inertia\Inertia;
use Inertia\Response;

class ProfileController
{
    public function __construct(private readonly ProfileService $profiles) {}

    public function edit(): Response
    {
        return Inertia::render('Profile/Edit');
    }

    public function update(Request $request): RedirectResponse
    {
        $user = $request->user();

        $data = $request->validate([
            'name' => ['required', 'string', 'max:100'],
            'username' => ['required', 'string', 'alpha_dash:ascii', 'min:3', 'max:30', Rule::unique('users', 'username')->ignore($user->id)],
            'password' => ['nullable', 'string', 'min:5', 'confirmed'],
            'photo' => ['nullable', 'image', 'max:2048'],
        ]);

        $this->profiles->update($user, $data, $request->file('photo'));

        return back()->with('success', 'Profil berhasil diperbarui.');
    }
}
