<?php

namespace App\Http\Resources;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;
use Illuminate\Support\Facades\Storage;

class UserResource extends JsonResource
{
    public static $wrap=false;
    /**
     * Transform the resource into an array.
     *
     * @return array<string, mixed>
     */
    public function toArray(Request $request): array
    {
        return [
            'avatar_path'=> Storage::disk('public')->url($this->avatar_path),
'cover_path' => Storage::disk('public')->url($this->cover_path),
'created_at'=>$this->created_at,
'email'=>$this->email,
'email_verified_at'=>$this->email_verified_at,
'id'=>$this->id,
'name'=>$this->name,
'updated_at'=>$this->updated_at,
'username'=>$this->username,
        ];
    }
}
