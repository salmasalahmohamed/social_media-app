<?php

namespace App\Http\Resources;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Str;

class GroupResource extends JsonResource
{
    /**
     * Transform the resource into an array.
     *
     * @return array<string, mixed>
     */
    public function toArray(Request $request): array
    {
        return [
            'id' => $this->id,
            'name' => $this->name,
            'slug' => $this->slug,
            'status' => $this->status,
            'role' => $this->role,
            'group_user' => $this->groupUser
                ? $this->groupUser->map(fn($u) => [
                    'id' => $u->id,
                    'user_id' => $u->user_id,
                    'group_id' => $u->group_id,
                    'role' => $u->role,
                    'status' => $u->status,
                ])
                : [],
            'posts'=>$this->posts? PostResource::collection($this->posts):[],

        'thumbnail_url' => $this->thumbnail_url ? Storage::url($this->thumbnail_url) : '/img/default.png',
            'cover_url' => $this->cover_path ? Storage::url($this->cover_path) : null,
            'auto_approval' => $this->auto_approval,
            'about' => $this->about,
            'description' => Str::words(strip_tags($this->about), 10),
            'user_id' => $this->user_id,
//            'deleted_at' => $this->deleted_at,
//            'deleted_by' => $this->deleted_by,
            'created_at' => $this->created_at,
            'updated_at' => $this->updated_at,
        ];    }
}
