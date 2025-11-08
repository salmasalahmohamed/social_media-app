<?php

namespace App\Models;

use App\Http\Enums\GroupUserStatus;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Notifications\Notifiable;

class Group extends Model
{
    use HasFactory, Notifiable;

    protected $guarded=[];
    public function groupUser(){
        return $this->hasMany(GroupUser::class,'group_id');
    }
    public function isAdmin(){
        return $this->role=="admin";
    }
    public function Admins(){
        return $this->belongsToMany(User::class,'group_users')->wherePivot('role','admin');
    }
    public function pendingUsers(){
        return $this->belongsToMany(User::class,'group_users')->wherePivot('status',GroupUserStatus::PENDING->value);
    }
    public function approvedUser(){
        return $this->belongsToMany(User::class,'group_users')->wherePivot('status',GroupUserStatus::APPROVED->value);
    }
    public function posts(){
        return $this->hasMany(Post::class);
    }

}
