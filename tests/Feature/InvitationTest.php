<?php

namespace Tests\Feature;

use App\Http\Enums\GroupUserRole;
use App\Http\Enums\GroupUserStatus;
use App\Models\Group;
use App\Models\GroupUser;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Foundation\Testing\WithFaker;
use Tests\TestCase;

class InvitationTest extends TestCase
{
    use RefreshDatabase;

    /** @test */
    public function test_admin_can_accept_invitation()
    {
        $admin = User::factory()->create();
        $user  = User::factory()->create();
        $group = Group::factory()->create(['user_id' => $admin->id,'auto_approval'=>false]);

        $groupUser = GroupUser::create([
            'group_id' => $group->id,
            'user_id' => $user->id,
            'created_by'=>$admin->id,
            'role'=>GroupUserRole::USER->value,
            'status' => GroupUserStatus::PENDING->value,
        ]);

        $this->actingAs($admin);

        $response = $this->post("/accept/invitation/".$user->id, [
            'group_user' => [
                ['group_id' => $group->id]
            ]
        ]);

        $response->assertStatus(302);

        $this->assertDatabaseHas('group_users', [
            'group_id' => $group->id,
            'user_id' => $user->id,
            'status' => GroupUserStatus::APPROVED->value
        ]);
    }
    /** @test */
    public function test_admin_can_reject_invitation()
    {
        $admin = User::factory()->create();
        $user  = User::factory()->create();
        $group = Group::factory()->create(['user_id' => $admin->id,'auto_approval'=>false]);

        $groupUser = GroupUser::create([
            'group_id' => $group->id,
            'user_id' => $user->id,
            'created_by'=>$admin->id,
            'role'=>GroupUserRole::USER->value,
            'status' => GroupUserStatus::PENDING->value,
        ]);

        $this->actingAs($admin);

        $response = $this->post("/reject/invitation/".$user->id, [
            'group_user' => [
                ['group_id' => $group->id]
            ]
        ]);

        $response->assertStatus(302);

        $this->assertDatabaseHas('group_users', [
            'group_id' => $group->id,
            'user_id' => $user->id,
            'status' => GroupUserStatus::REJECTED->value
        ]);
    }
}
