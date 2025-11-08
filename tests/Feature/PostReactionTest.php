<?php

namespace Tests\Feature;

use App\Models\Post;
use App\Models\Reaction;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class PostReactionTest extends TestCase
{
    use RefreshDatabase;

    /** @test */
    public function test_user_can_like_and_unlike_post()
    {
        $user = User::factory()->create();
        $post = Post::create(['user_id'=>$user->id,
            'body'=>'new test post'
        ]);
        $response = $this->actingAs($user)->post(route('post.reaction', $post->id));
        $response->assertOk();
        $response->assertJson([
            'success' => true,
            'react_number' => 1,
            'has_react' => true,
        ]);

        $this->assertDatabaseHas('reactions', [
            'object_id' => $post->id,
            'user_id' => $user->id,
        ]);

        $response = $this->actingAs($user)->post(route('post.reaction', $post->id));
        $response->assertOk();
        $response->assertJson([
            'success' => true,
            'react_number' => 0,
            'has_react' => false,
        ]);

        $this->assertDatabaseMissing('reactions', [
            'object_id' => $post->id,
            'user_id' => $user->id,
        ]);
    }
}
