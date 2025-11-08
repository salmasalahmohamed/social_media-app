<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\MorphTo;

class Reaction extends Model
{
    public $guarded=[];
    public function object(): MorphTo
    {
        return $this->morphTo();
    }
}
