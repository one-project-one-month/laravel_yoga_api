<?php

namespace App\Http\Resources\Auth;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class AuthResource extends JsonResource
{
    /**
     * Transform the resource into an array.
     *
     * @return array<string, mixed>
     */
    public function toArray(Request $request): array
    {
        return [
            'user' => [
                'id' => $this['user']->id,
                'name' => $this['user']->name, //change user_name to name
                'email' => $this['user']->email,
                'roleId' => $this['user']->role_id,
                'createdAt' => $this['user']->created_at,
                'updatedAt' => $this['user']->updated_at,
            ],
            'token' => $this['token']
        ];
    }
}
