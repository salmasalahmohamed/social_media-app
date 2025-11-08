<?php

namespace Tests\Feature;

use App\Models\Follwor;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Foundation\Testing\WithFaker;
use Tests\TestCase;

class FollowUserTest extends TestCase
{
    use RefreshDatabase;

    /** @test */
    public function test_user_can_follow_other_user()
    {
        $me = User::factory()->create();
        $other = User::factory()->create();
        $this->actingAs($me);


        $response = $this->post(route('user.follow', $other), [
            'follow' => true,
        ]);

        $response->assertRedirect();

        $this->assertDatabaseHas('follwors', [
            'user_id' => $other->id,
            'follower_id' => $me->id,
        ]);
    }

    /** @test */
    public function test_user_can_unfollow_other_user()
    {
        $me = User::factory()->create();
        $other = User::factory()->create();
        $this->actingAs($me);

        Follwor::create([
            'user_id' => $other->id,
            'follower_id' => $me->id,
        ]);

        $response = $this->post(route('user.follow', $other), [
            'follow' => false,
        ]);

        $response->assertRedirect();

        $this->assertDatabaseMissing('follwors', [
            'user_id' => $other->id,
            'follower_id' => $me->id,
        ]);
    }
}
