<?php

namespace App\Http\Resources\Dashboard;

use Illuminate\Http\Resources\Json\JsonResource;
use Illuminate\Http\Request;

/**
 * @OA\Schema(
 * schema="SubscriptionResource",
 * title="Subscription Resource",
 * description="Subscription model representation",
 * @OA\Property(property="id", type="integer", description="Subscription ID", example=1),
 * @OA\Property(property="name", type="string", description="Name of the subscription", example="Yoga"),
 * @OA\Property(property="description", type="text", description="Description of the subscription", example="Lorem ipsum dolor sit amet consectetur adipisicing elit. Inventore, quae!"),
 * @OA\Property(property="price", type="integer", description="Price of the subscription", example=350000),
 * @OA\Property(property="lessonTypeId", type="integer", description="Lesson type of the subscription", example=2),
 * @OA\Property(property="duration", type="string", description="Duration of the subscription", example="3 months"),
 * @OA\Property(property="createdAt", type="string", format="date-time", description="Creation timestamp", example="2023-10-28T12:00:00.000000Z"),
 * @OA\Property(property="updatedAt", type="string", format="date-time", description="Last update timestamp", example="2023-10-28T12:00:00.000000Z")
 * )
 */
class SubscriptionResource extends JsonResource
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
            'description' => $this->description,
            'price' => $this->price,
            'lessonTypeId' => $this->lesson_type_id,
            'duration' => $this->duration,
            'createdAt' => $this->created_at,
            'updatedAt' => $this->updated_at,
        ];
    }
}
