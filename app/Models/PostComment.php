<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\MorphMany;

class PostComment extends Model
{
    public $guarded=[];
    public function reactions(): MorphMany
    {
        return $this->morphMany(Reaction::class, 'object');
    }
}
