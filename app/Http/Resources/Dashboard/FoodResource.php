<?php

namespace App\Http\Resources\Dashboard;

use Illuminate\Http\Resources\Json\JsonResource;
use Illuminate\Http\Request;

/**
 * @OA\Schema(
 * schema="FoodResource",
 * title="Food Resource",
 * description="Food model representation",
 * @OA\Property(property="id", type="integer", description="Food ID", example=2),
 * @OA\Property(property="userId", type="integer", description="User of the food", example=1),
 * @OA\Property(property="user", ref="#/components/schemas/UserResource"),
 * @OA\Property(property="title", type="string", description="Title of the food", example="Doloremque blanditi"),
 * @OA\Property(property="ingredients", type="string", description="Ingredients of the food", example="Milk"),
 * @OA\Property(property="createdBy", type="integer", description="Created by of the food", example="3"),
 * @OA\Property(property="nutrition", type="string", description="Nutrition of the food", example="Voluptas explicabo rerum molestiae totam repudiandae ut. Sapiente eos doloremque veritatis voluptatem. Amet voluptatem sed itaque sed non."),
 * @OA\Property(property="imageUrl", type="text", description="Image url of the food", example="foodexample.jpg"),
 * @OA\Property(property="imagePublicId", type="string", description="Image Public Id of the food", example="2"),
 * @OA\Property(property="description", type="text", description="Desctiption of the food", example="Lorem ipsum dolor sit amet consectetur adipisicing elit. Laudantium reprehenderit incidunt nemo ipsam qui. Architecto, autem officiis ipsum dolores illo neque dolorum accusantium fugit ab, nemo cum magni illum veniam!"),
 * @OA\Property(property="rating", type="string", description="Rating of the food", example="5"),
 * @OA\Property(property="createdAt", type="string", format="date-time", description="Creation timestamp", example="2023-10-28T12:00:00.000000Z"),
 * @OA\Property(property="updatedAt", type="string", format="date-time", description="Last update timestamp", example="2023-10-28T12:00:00.000000Z")
 * )
 */
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
