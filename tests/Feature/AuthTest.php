<?php

namespace Tests\Feature;

use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class AuthTest extends TestCase
{
    use RefreshDatabase;

    public function test_login_and_register_pages_render_for_guest(): void
    {
        $this->get('/login')->assertOk();
        $this->get('/register')->assertOk();
    }

    public function test_member_login_redirects_to_dashboard(): void
    {
        $user = User::factory()->create(['username' => 'budi', 'password' => 'rahasia']);

        $this->post('/login', ['username' => 'budi', 'password' => 'rahasia', 'remember' => true])
            ->assertRedirect('/dashboard/posts');
        $this->assertAuthenticatedAs($user);
    }

    public function test_admin_login_redirects_to_admin_panel(): void
    {
        User::factory()->admin()->create(['username' => 'boss', 'password' => 'rahasia']);

        $this->post('/login', ['username' => 'boss', 'password' => 'rahasia'])->assertRedirect('/admin/users');
    }

    public function test_invalid_credentials_are_rejected(): void
    {
        User::factory()->create(['username' => 'budi', 'password' => 'rahasia']);

        $this->post('/login', ['username' => 'budi', 'password' => 'salah'])->assertSessionHasErrors('username');
        $this->post('/login', [])->assertSessionHasErrors(['username', 'password']);
        $this->assertGuest();
    }

    public function test_register_creates_member_and_logs_in(): void
    {
        $this->post('/register', [
            'name' => 'Siti', 'username' => 'siti_01', 'password' => 'abcde', 'password_confirmation' => 'abcde',
        ])->assertRedirect('/dashboard/posts');

        $this->assertDatabaseHas('users', ['username' => 'siti_01', 'role' => 'member']);
        $this->assertAuthenticated();
    }

    public function test_register_validates_unique_username(): void
    {
        User::factory()->create(['username' => 'siti']);

        $this->post('/register', ['name' => 'S', 'username' => 'siti', 'password' => 'abcde', 'password_confirmation' => 'abcde'])
            ->assertSessionHasErrors('username');
    }

    public function test_logout(): void
    {
        $this->actingAs(User::factory()->create())->post('/logout')->assertRedirect('/');
        $this->assertGuest();
    }

    public function test_authenticated_user_is_redirected_away_from_login(): void
    {
        $this->actingAs(User::factory()->create())->get('/login')->assertRedirect('/dashboard/posts');
        $this->actingAs(User::factory()->admin()->create())->get('/login')->assertRedirect('/admin/users');
    }

    public function test_guest_is_redirected_from_dashboard(): void
    {
        $this->get('/dashboard/posts')->assertRedirect('/login');
        $this->actingAs(User::factory()->create())->get('/dashboard')->assertRedirect('/dashboard/posts');
    }
}
