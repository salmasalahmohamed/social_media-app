<?php

namespace Tests\Feature;

use App\Models\Post;
use App\Models\PostAttachment;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\Storage;
use Tests\TestCase;

class PostCreateTest extends TestCase
{
    use RefreshDatabase;

    /** @test */
    public function test_user_can_create_post_with_attachments()
    {
        Storage::fake('public');

        $user = User::factory()->create();
        $this->actingAs($user);

        $file1 = UploadedFile::fake()->image('a.jpg');
        $file2 = UploadedFile::fake()->image('b.jpg');

        $response = $this->post(route('post.store'), [
            'user_id'=>$user->id,
            'body'=>'new test post',
            'attachments' => [$file1, $file2],
        ]);

        $response->assertRedirect();
        $response->assertSessionHas('success','post created');
        $this->assertDatabaseHas('posts', [
            'body' => 'new test post',
        ]);

        $post = Post::first();

        $this->assertDatabaseHas('post_attachments', [
            'post_id' => $post->id,
            'name'   => 'a.jpg',
        ]);

        Storage::disk('public')->assertExists("attachment-{$post->id}/{$file1->hashName()}");
        Storage::disk('public')->assertExists("attachment-{$post->id}/{$file2->hashName()}");
   }
}
