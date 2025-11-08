<?php

namespace Tests\Feature;

use App\Models\Post;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Foundation\Testing\WithFaker;
use Tests\TestCase;

class CommentCreateTest extends TestCase
{
use RefreshDatabase;

    /** @test */
    public function test_user_can_create_comment_on_post()
    {
        $user = User::factory()->create();
        $post = Post::create(['user_id'=>$user->id,
            'body'=>'new test post'
        ]);

        $response = $this->actingAs($user)->post(route('post.comment', $post->id), [
            'comment' => 'This is a test comment',
        ]);

        $response->assertStatus(201);

        $this->assertDatabaseHas('post_comments', [
            'post_id' => $post->id,
            'user_id' => $user->id,
            'comment' => 'This is a test comment'
        ]);

        $response->assertJson([
            'success' => true,
        ]);
    }

    /** @test */
    public function test_comment_is_required()
    {
        $user = User::factory()->create();
        $post = Post::create(['user_id'=>$user->id,
            'body'=>'new test post'
        ]);
        $response = $this->actingAs($user)->post(route('post.comment', $post->id), [
            'comment' => '',
        ]);

        $response->assertSessionHasErrors(['comment']);
    }
}
