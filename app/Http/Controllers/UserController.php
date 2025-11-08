<?php

namespace App\Http\Controllers;

use App\Models\Follwor;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class UserController extends Controller
{
    public function follow( Request $request,User$user){
        $data=$request->validate([
            'follow'=>'boolean',
        ]);
        if ($data['follow']){
            $message='you followed user';
            Follwor::create([
                'user_id'=>$user->id,
                'follower_id'=>Auth::user()->id,
            ]);
        }else{
            $message='you unfollowed user';
           Follwor::where('user_id',$user->id)->where('follower_id',Auth::id())->delete();

        }

 return back()->with('success',$message.$user->name);
    }
}
