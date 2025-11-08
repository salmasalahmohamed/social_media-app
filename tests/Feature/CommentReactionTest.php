<?php

namespace Tests\Feature;

use App\Models\Post;
use App\Models\PostComment;
use App\Models\Reaction;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class CommentReactionTest extends TestCase
{
    use RefreshDatabase;

    /** @test */
    public function test_user_can_like_and_unlike_comment()
    {
        $user = User::factory()->create();
        $post = Post::create(['user_id'=>$user->id,
            'body'=>'new test post'
        ]);
        $comment = PostComment::create([
            'post_id' => $post->id,
            'user_id' => $user->id,
            'comment'=>'comment'
        ]);

        $response = $this->actingAs($user)->post(route('comment.reaction', $comment->id));
        $response->assertOk();
        $response->assertJson([
            'success' => true,
            'react_number' => 1,
            'has_react' => true,
        ]);

        $this->assertDatabaseHas('reactions', [
            'object_id' => $comment->id,
            'user_id' => $user->id,
        ]);

        $response = $this->actingAs($user)->post(route('comment.reaction', $comment->id));
        $response->assertOk();
        $response->assertJson([
            'success' => true,
            'react_number' => 0,
            'has_react' => false,
        ]);

        $this->assertDatabaseMissing('reactions', [
            'object_id' => $comment->id,
            'user_id' => $user->id,
        ]);
    }
}
