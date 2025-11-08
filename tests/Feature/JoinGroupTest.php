<?php

namespace Tests\Feature;

use App\Http\Enums\GroupUserRole;
use App\Http\Enums\GroupUserStatus;
use App\Models\Group;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Foundation\Testing\WithFaker;
use Tests\TestCase;

class JoinGroupTest extends TestCase
{
    use RefreshDatabase;

    /** @test */
    public function test_user_can_join_group_with_auto_approval()
    {
        $useradmin = User::factory()->create();

        $user = User::factory()->create();

        $group = Group::factory()->create([
            'auto_approval' => true,
            'user_id' => $useradmin->id
        ]);

        $this->actingAs($user);

        $response = $this->post(route('group.joinGroup', $group->slug));

        $response->assertStatus(302);

        $this->assertDatabaseHas('group_users', [
            'user_id' => $user->id,
            'group_id' => $group->id,
            'status' => GroupUserStatus::APPROVED->value,
            'role' => GroupUserRole::USER->value
        ]);
    }
}
