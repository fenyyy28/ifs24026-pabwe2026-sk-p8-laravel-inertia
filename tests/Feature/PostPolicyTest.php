<?php

namespace Tests\Feature;

use App\Features\Post\Models\Post;
use App\Features\Post\Policies\PostPolicy;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class PostPolicyTest extends TestCase
{
    use RefreshDatabase;

    public function test_only_owner_can_view_update_and_delete(): void
    {
        $owner = User::factory()->create();
        $other = User::factory()->create();
        $post = Post::factory()->for($owner)->create();
        $policy = new PostPolicy;

        $this->assertTrue($policy->view($owner, $post));
        $this->assertTrue($policy->update($owner, $post));
        $this->assertTrue($policy->delete($owner, $post));

        $this->assertFalse($policy->view($other, $post));
        $this->assertFalse($policy->update($other, $post));
        $this->assertFalse($policy->delete($other, $post));
    }
}