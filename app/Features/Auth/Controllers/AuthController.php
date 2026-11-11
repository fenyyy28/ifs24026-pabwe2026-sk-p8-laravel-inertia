<?php

namespace App\Features\Auth\Controllers;

use App\Features\Auth\Actions\LoginUserAction;
use App\Features\Auth\Actions\RegisterUserAction;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Inertia\Inertia;
use Inertia\Response;

class AuthController
{
    public function showLogin(): Response
    {
        return Inertia::render('Auth/Login');
    }

    public function login(Request $request, LoginUserAction $action): RedirectResponse
    {
        $data = $request->validate([
            'username' => ['required', 'string'],
            'password' => ['required', 'string'],
        ]);

        $action->execute($data['username'], $data['password'], $request->boolean('remember'));
        $request->session()->regenerate();

        return redirect()->intended($request->user()->isAdmin() ? '/admin/users' : '/dashboard/posts');
    }

    public function showRegister(): Response
    {
        return Inertia::render('Auth/Register');
    }

    public function register(Request $request, RegisterUserAction $action): RedirectResponse
    {
        $data = $request->validate([
            'name' => ['required', 'string', 'max:100'],
            'username' => ['required', 'string', 'alpha_dash:ascii', 'min:3', 'max:30', 'unique:users,username'],
            'password' => ['required', 'string', 'min:5', 'confirmed'],
        ]);

        $action->execute($data);
        $request->session()->regenerate();

        return to_route('posts.index')->with('success', 'Selamat datang di BlogVerse Pro!');
    }

    public function logout(Request $request): RedirectResponse
    {
        Auth::logout();
        $request->session()->invalidate();
        $request->session()->regenerateToken();

        return to_route('home');
    }
}
