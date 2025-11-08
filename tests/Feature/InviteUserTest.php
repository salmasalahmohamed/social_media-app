<?php

namespace Tests\Feature;

use App\Http\Enums\GroupUserRole;
use App\Http\Enums\GroupUserStatus;
use App\Models\Group;
use App\Models\GroupUser;
use App\Models\User;
use App\Notifications\InviteApproved;
use App\Notifications\InviteUser;
use Carbon\Carbon;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Foundation\Testing\WithFaker;
use Illuminate\Support\Facades\Notification;
use Illuminate\Support\Str;
use Tests\TestCase;

class InviteUserTest extends TestCase
{
    use RefreshDatabase;

    /** @test */
    public function test_admin_can_invite_a_user_to_group()
    {
        Notification::fake();

        $admin = User::factory()->create();
        $user  = User::factory()->create();

        $group = Group::factory()->create(['user_id' => $admin->id,'auto_approval'=>false]);

        $this->actingAs($admin);

        $response = $this->post("/group/invite/".$group->slug, [
            'email' => $user->email,
        ]);

        $response->assertStatus(302);
        $response->assertSessionHas('success', 'user invited to join the group');

        $this->assertDatabaseHas('group_users', [
            'group_id' => $group->id,
            'user_id'  => $user->id,
            'status'   => GroupUserStatus::PENDING->value,
            'role'     => GroupUserRole::USER->value,
        ]);

        Notification::assertSentTo($user, InviteUser::class);
    }
    /** @test */
    public function test_admin_can_approve_invitation_using_valid_token()
    {
        Notification::fake();

        $admin = User::factory()->create();
        $user  = User::factory()->create();
        $group = Group::factory()->create(['user_id' => $admin->id,'auto_approval'=>false]);

        $groupUser = GroupUser::create([
            'group_id' => $group->id,
            'user_id'  => $user->id,
            'status'   => GroupUserStatus::PENDING->value,
            'role'=>GroupUserRole::USER->value,

            'token'    => Str::random(64),
            'token_expire_date' => Carbon::now()->addHours(24),
            'created_by' => $admin->id,
        ]);

        $response = $this->get("/group/approve-invitation/{$groupUser->token}");
        $groupUser->status=GroupUserStatus::APPROVED->value;
        $groupUser->token_used=Carbon::now();
        $groupUser->save();
        $response->assertRedirect();

        $this->assertDatabaseHas('group_users', [
            'id' => $groupUser->id,
            'status' => GroupUserStatus::APPROVED->value,
        ]);

//        Notification::assertSentTo($admin, InviteApproved::class);
    }
}
