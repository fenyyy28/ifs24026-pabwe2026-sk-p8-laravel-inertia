<?php

namespace Tests\Feature;

use App\Features\Post\Models\Post;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Inertia\Testing\AssertableInertia as Assert;
use Tests\TestCase;

class AdminTest extends TestCase
{
    use RefreshDatabase;

    private User $admin;

    protected function setUp(): void
    {
        parent::setUp();
        $this->admin = User::factory()->admin()->create();
    }

    public function test_member_and_guest_cannot_access_admin(): void
    {
        $this->get('/admin/users')->assertRedirect('/login');
        $this->actingAs(User::factory()->create())->get('/admin/users')->assertForbidden();
        $this->actingAs(User::factory()->create())->get('/admin/posts')->assertForbidden();
    }

    public function test_user_list_with_search(): void
    {
        User::factory()->create(['name' => 'Cari Aku', 'username' => 'cariaku']);

        $this->actingAs($this->admin)->get('/admin/users')->assertInertia(fn (Assert $p) => $p
            ->component('Admin/UserList')->has('users.data', 2));
        $this->actingAs($this->admin)->get('/admin/users?search=cariaku')->assertInertia(fn (Assert $p) => $p->has('users.data', 1));
    }

    public function test_change_role_and_delete_user(): void
    {
        $member = User::factory()->create();

        $this->actingAs($this->admin)->patch("/admin/users/{$member->id}/role", ['role' => 'admin'])->assertSessionHas('success');
        $this->assertTrue($member->refresh()->isAdmin());

        $this->actingAs($this->admin)->patch("/admin/users/{$member->id}/role", ['role' => 'x'])->assertSessionHasErrors('role');

        $this->actingAs($this->admin)->delete("/admin/users/{$member->id}")->assertSessionHas('success');
        $this->assertModelMissing($member);
    }

    public function test_admin_cannot_modify_or_delete_self(): void
    {
        $this->actingAs($this->admin)->patch("/admin/users/{$this->admin->id}/role", ['role' => 'member'])->assertSessionHas('error');
        $this->actingAs($this->admin)->delete("/admin/users/{$this->admin->id}")->assertSessionHas('error');

        $this->assertTrue($this->admin->refresh()->isAdmin());
    }

    public function test_monitor_all_and_user_posts(): void
    {
        $author = User::factory()->create();
        Post::factory()->published()->for($author)->count(2)->create();
        Post::factory()->for($author)->create();
        Post::factory()->create();

        $this->actingAs($this->admin)->get('/admin/posts')->assertInertia(fn (Assert $p) => $p
            ->component('Admin/PostList')->has('posts.data', 4));
        $this->actingAs($this->admin)->get("/admin/users/{$author->id}/posts")->assertInertia(fn (Assert $p) => $p
            ->component('Admin/UserPostList')->has('posts.data', 3)->where('stats.published', 2));
    }

    public function test_admin_exports(): void
    {
        $author = User::factory()->create(['username' => 'penulis']);
        Post::factory()->for($author)->create();

        $all = $this->actingAs($this->admin)->get('/admin/posts/export');
        $all->assertOk()->assertDownload('semua-artikel.xlsx');
        $this->assertNotSame('', $all->streamedContent());

        $mine = $this->actingAs($this->admin)->get("/admin/users/{$author->id}/posts/export");
        $mine->assertOk()->assertDownload('artikel-penulis.xlsx');
        $this->assertNotSame('', $mine->streamedContent());
    }
}