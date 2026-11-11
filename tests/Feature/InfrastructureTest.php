<?php

namespace Tests\Feature;

use App\Http\Middleware\EnsureRole;
use App\Models\User;
use Database\Seeders\DatabaseSeeder;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Http\Request;
use Inertia\Testing\AssertableInertia as Assert;
use Tests\TestCase;

class InfrastructureTest extends TestCase
{
    use RefreshDatabase;

    public function test_inertia_shares_auth_and_categories(): void
    {
        $user = User::factory()->create(['name' => 'Tono']);

        $this->actingAs($user)->get('/')->assertInertia(fn (Assert $p) => $p
            ->where('auth.user.name', 'Tono')->has('categories', 6));
        $this->get('/')->assertOk();
    }

    public function test_seeder_creates_admin_and_member_once(): void
    {
        $this->seed(DatabaseSeeder::class);
        $this->seed(DatabaseSeeder::class);

        $this->assertTrue(User::where('username', 'admin')->firstOrFail()->isAdmin());
        $this->assertSame(9, User::where('username', 'member')->firstOrFail()->posts()->count());
    }

    public function test_force_https_config_forces_scheme(): void
    {
        config(['app.force_https' => true]);
        (new \App\Providers\AppServiceProvider($this->app))->boot();

        $this->assertStringStartsWith('https://', url('/x'));
    }

    public function test_ensure_role_middleware_denies_guest(): void
    {
        $this->expectException(\Symfony\Component\HttpKernel\Exception\HttpException::class);

        (new EnsureRole)->handle(Request::create('/'), fn () => response('ok'), 'admin');
    }
}
