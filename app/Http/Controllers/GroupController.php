<?php

namespace App\Http\Controllers;

use App\Http\Enums\GroupUserRole;
use App\Http\Enums\GroupUserStatus;
use App\Http\Resources\GroupResource;
use App\Http\Resources\UserResource;
use App\Models\Group;
use App\Models\GroupUser;
use App\Models\Notification;
use App\Models\User;
use App\Notifications\InviteApproved;
use App\Notifications\InviteUser;
use App\Notifications\RequestToJoinGroup;
use App\Rules\ExistInUsernameOrEmail;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Mail;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Facades\Validator;
use Illuminate\Support\Str;
use Inertia\Inertia;
use Symfony\Component\HttpFoundation\Exception\BadRequestException;

class GroupController extends Controller
{
    public function profile($slug){

        $currntgroup=Group::where('slug','=',$slug)->with(['groupUser','posts'])->first();
        $pendingUser=$currntgroup->pendingUsers;
        $approved=$currntgroup->approvedUser;
        return Inertia::render('Group/view', [
            'success' => session('success'),
            'group' => new GroupResource($currntgroup),
            'pendingUser'=>$pendingUser,
            'approvedUser'=>$approved,

        ]);

    }
    public function create(Request$request){
        $request->validate([
            'name'=>'required',
            'about'=>'required',
            'auto_approval'=>'required|boolean'
        ]);
      $group= Group::query()->create([
            'name'=>$request->name,
            'about'=>$request->about,
            'user_id'=>$request->user()->id,
            'slug'=>Str::slug($request->name),
            'auto_approval'=>$request->auto_approval,

        ]);
        $groupUserData = [
            'status' => GroupUserStatus::APPROVED->value,
            'role' => GroupUserRole::ADMIN->value,
            'user_id' => Auth::id(),
            'group_id' => $group->id,
            'created_by' => Auth::id()
        ];
        $group->status = $groupUserData['status'];
        $group->role = $groupUserData['role'];
$group->save();
        GroupUser::create($groupUserData);


        return response(new GroupResource($group), 201);
    }
    public function updateImage(Request $request,Group $group)

    {
         if(!$group->isAdmin()){
             return response('you are not the admin',404);
         }

        $data = $request->validate([
            'cover' => ['nullable', 'image'],
            'thumbnail' => ['nullable', 'image']
        ]);

        $user = $request->user();

        $cover = $data['cover'] ?? null;
        /** @var \Illuminate\Http\UploadedFile $cover */
        $thumbnail = $data['thumbnail'] ?? null;
        $success = '';
        if ($cover) {
            if ($group->cover_path) {
                Storage::disk('public')->delete($group->cover_path);
            }

            $path = $cover->store('group-' . $group->id, 'public');
            $group->update(['cover_path' => $path]);
            $success = 'Your cover image was updated';

        }

        if ($thumbnail) {
            if ($group->thumbnail_url) {
                Storage::disk('public')->delete($group->thumbnail_url);
            }
            $path = $thumbnail->store('group-' . $group->id, 'public');
            $group->update(['thumbnail_url' => $path]);
            $success = 'Your avatar image was updated';
        }


        return back()->with('success', $success);
    }
    public function inviteUser(Group $group,Request $request){
        $validator = Validator::make($request->all(), [
            'email' => ['required',new ExistInUsernameOrEmail()],
        ]);
        if ($validator->fails()){
            return response()->json([
                'errors' => $validator->errors(),
            ], 422);        }
        $user=User::where('email',$request['email'])->orWhere('name',$request['email'])->first();

           $groupUser=GroupUser::where('user_id',$user->id)->where('group_id',$group->id)->first();
           if($groupUser && $groupUser->status==GroupUserStatus::APPROVED->value){
               return back()->with('success','user already joined');

           }
        if($groupUser && $groupUser->status==GroupUserStatus::PENDING->value){
            $groupUser->delete();
        }

       $current_group= GroupUser::create([
            'status'=>GroupUserStatus::PENDING->value,
            'role'=>GroupUserRole::USER->value,
            'token'=>Str::random(256),
            'token_expire_date'=>Carbon::now()->addHours(24),
            'user_id'=>$user->id,
            'group_id'=>$group->id,
            'created_by'=>$request->user()->id,
        ]);

        $user->notify(new InviteUser($group,$current_group->token));
        return back()->with('success','user invited to join the group');
    }
    public function approveInvitation($token){
 $groupuser=GroupUser::where('token',$token)->first();
 if (!$groupuser){
     throw new BadRequestException('the token is not valid');
 }elseif ($groupuser->token_expire_date<Carbon::now()){
     throw new BadRequestException('the token expired');

 }elseif ($groupuser->status===GroupUserStatus::APPROVED->value){
     throw new BadRequestException('the link already used');

 }
 $groupuser->status=GroupUserStatus::APPROVED->value;
 $groupuser->token_used=Carbon::now();
 $groupuser->save();
 $admin=$groupuser->creatd_by;
 $admin->notify(new InviteApproved($groupuser));
 return redirect(route('group.profile',$groupuser->group));




    }
    public function joinGroup(Group $group,Request $request){
     $user=$request->user();
     $status=GroupUserStatus::APPROVED->value;
     $message='you joined the group successfully ';

        if( !$group->auto_approval) {
         $status=GroupUserStatus::PENDING->value;
$message='your request sent to admin'
;
Notification::send($group->Admins,new RequestToJoinGroup($group,$user));

     }
         GroupUser::create([
             'status' => $status,
             'role' => GroupUserRole::USER->value,
             'user_id' => $user->id,
             'group_id' => $group->id,
             'created_by' => $group->user_id,
         ]);

        return back()->with('success',$message);

    }
    public function acceptInvitation($user_id,Request$request){
        foreach ($request->group_user as  $key=>$groupUser) {
            $record = GroupUser::where('group_id', $groupUser['group_id'])
                ->where('user_id', $user_id)
                ->first();

            if ($record) {
                $record->status = GroupUserStatus::APPROVED->value;
                $record->save();
            }
        }
        return back()->with('success','accept invitation');

    }
    public function rejectInvitation($user_id,Request$request){

        foreach ($request->group_user as  $groupUser) {
            $record = GroupUser::where('group_id', $groupUser['group_id'])
                ->where('user_id', $user_id)
                ->first();

            if ($record) {
                $record->status = GroupUserStatus::REJECTED->value;
                $record->save();
            }        }
        //send email
        return back()->with('success','reject invitation');

    }
    public function removeUser($user_id,Request$request){
        if ($request->user()->id!=$request->user_id){
            return 'u ar not allowed to do this action';
        }else{
           $group_user=GroupUser::query()->where('user_id',$user_id)->where('created_by',$request->user_id)->first();
           $group_user->delete();
        }

    }
}
