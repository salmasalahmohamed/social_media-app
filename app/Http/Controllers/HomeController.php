<?php

namespace App\Http\Controllers;

use App\Http\Enums\GroupUserStatus;
use App\Http\Resources\GroupResource;
use App\Http\Resources\PostResource;
use App\Models\Group;
use App\Models\Post;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Inertia\Inertia;
use Illuminate\Support\Facades\Route;
use Illuminate\Foundation\Application;

class HomeController extends Controller
{
    public function index(){
        $user=Auth::user();
        $id=$user->id;
        $posts=Post::where('user_id','=',$id)->orWhere(function($q){
            $q->whereIn('user_id', auth()->user()->followings()->pluck('users.id'))
                ->orWhere('user_id', auth()->id());})
            ->whereIn('group_id', auth()->user()->ownedGroups()->pluck('groups.id'))
            ->withCount('reactions')->with('comment')->with(['reactions'=>function ($q)use($id){
        }])->latest()->paginate(10);
        $group = Group::
            with(['groupUser' => function ($q)use($id) {
                $q->where('user_id', $id)
            ;

            }])
            ->get();
        $follower=User::find($user->id);
        $followings=$follower->followings;
        return Inertia::render('Welcome', [
            'canLogin' => Route::has('login'),
            'canRegister' => Route::has('register'),
            'laravelVersion' => Application::VERSION,
            'phpVersion' => PHP_VERSION,
            'posts'=> PostResource::collection($posts),
            'groups'=>GroupResource::collection($group),
            'users'=>$followings

        ]);
    }
    public function posts(){
        $user=Auth::user();
        $id=$user->id;
        $posts=Post::withCount('reactions')->with('comment')->with(['reactions'=>function ($q)use($id){
            $q->where('user_id','=',$id);
        }])->latest()->paginate(3);
        return response()->json($posts);

    }
}
