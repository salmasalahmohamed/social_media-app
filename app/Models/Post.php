<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\MorphMany;
use Illuminate\Database\Eloquent\SoftDeletes;

class Post extends Model
{
    use SoftDeletes;
    public $guarded=[];
    public function user(){
        return $this->belongsTo(User::class);
    }

    public function group(){
        return $this->belongsTo(Group::class);
    }

    public function attachment(){
        return $this->hasMany(PostAttachment::class);
    }
    public function reactions(): MorphMany
    {
        return $this->morphMany(Reaction::class, 'object');
    }
    public function comment(){
        return $this->hasMany(PostComment::class);
    }

}
