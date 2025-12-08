<?php

namespace App\Http\Resources\Client;

use App\Http\Resources\Dashboard\UserResource;
use Illuminate\Http\Resources\Json\JsonResource;
use Illuminate\Http\Request;

class FoodResource extends JsonResource
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
            'userId' => $this->user_id,
            'user' => new UserResource($this->whenLoaded('user')),
            'title' => $this->title,
            'ingredients' => $this->ingredients,
            'createdBy' => $this->created_by,
            'nutrition' => $this->nutrition,
            'imageUrl' => $this->image_url,
            'imagePublicId' => $this->image_public_id,
            'description' => $this->description,
            'rating' => $this->rating
        ];
    }
}
