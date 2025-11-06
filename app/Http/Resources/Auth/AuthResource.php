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
        $data = [
            'user' => [
                'id' => $this['user']->id,
                'fullName' => $this['user']->full_name,
                'email' => $this['user']->email,
            ],
            'token' => $this['token'] ?? $this['accessToken'] ?? null,
        ];

        if (isset($this['refreshToken'])) {
            $data['refresh_token'] = $this['refreshToken'];
        } elseif (isset($this['refresh_token'])) {
            $data['refresh_token'] = $this['refresh_token'];
        }

        return $data;
    }
}
