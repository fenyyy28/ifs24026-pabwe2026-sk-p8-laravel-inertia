<?php

namespace Database\Seeders;

use App\Features\Post\Models\Post;
use App\Models\User;
use Illuminate\Database\Seeder;

class DatabaseSeeder extends Seeder
{
    public function run(): void
    {
        User::updateOrCreate(
            ['username' => 'admin'],
            ['name' => 'Administrator', 'password' => 'admin', 'role' => User::ROLE_ADMIN]
        );

        $member = User::updateOrCreate(
            ['username' => 'member'],
            ['name' => 'Penulis Contoh', 'password' => 'member', 'role' => User::ROLE_MEMBER]
        );

        if ($member->posts()->doesntExist()) {
            Post::factory()->count(6)->published()->for($member)->create();
            Post::factory()->count(3)->for($member)->create();
        }
    }
}
