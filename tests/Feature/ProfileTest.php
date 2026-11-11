<?php

namespace Tests\Feature;

use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Storage;
use Tests\TestCase;

class ProfileTest extends TestCase
{
    use RefreshDatabase;

    public function test_edit_page_renders(): void
    {
        $this->actingAs(User::factory()->create())->get('/profile')->assertOk();
    }

    public function test_update_profile_with_password_and_photo(): void
    {
        Storage::fake('public');
        $user = User::factory()->create();

        $payload = ['name' => 'Baru', 'username' => 'baru', 'password' => 'abcdef', 'password_confirmation' => 'abcdef', 'photo' => UploadedFile::fake()->image('p.jpg')];
        $this->actingAs($user)->post('/profile', $payload)->assertSessionHas('success');

        $user->refresh();
        $this->assertSame('baru', $user->username);
        $this->assertTrue(Hash::check('abcdef', $user->password));
        Storage::disk('public')->assertExists($user->profile_photo_path);
        $this->assertNotNull($user->profile_photo_url);

        $old = $user->profile_photo_path;
        $this->actingAs($user)->post('/profile', ['name' => 'Baru', 'username' => 'baru', 'photo' => UploadedFile::fake()->image('q.jpg')]);
        Storage::disk('public')->assertMissing($old);
        $this->assertTrue(Hash::check('abcdef', $user->refresh()->password));
    }

    public function test_username_must_be_unique_except_own(): void
    {
        User::factory()->create(['username' => 'dipakai']);
        $user = User::factory()->create(['username' => 'saya']);

        $this->actingAs($user)->post('/profile', ['name' => 'X', 'username' => 'dipakai'])->assertSessionHasErrors('username');
        $this->actingAs($user)->post('/profile', ['name' => 'X', 'username' => 'saya'])->assertSessionDoesntHaveErrors();
    }

    public function test_user_without_photo_has_null_url(): void
    {
        $this->assertNull(User::factory()->make()->profile_photo_url);
    }
}
