<?php

namespace App\Http\Resources\Dashboard;

use Illuminate\Http\Resources\Json\JsonResource;
use Illuminate\Http\Request;

/**
 * @OA\Schema(
 * schema="LessonTypeResource",
 * title="Lesson Type Resource",
 * description="Lesson Type model representation",
 * @OA\Property(property="id", type="integer", description="Lesson type ID", example=2),
 * @OA\Property(property="name", type="string", description="name of the lesson type", example="architecto"),
 * @OA\Property(property="description", type="string", description="Description of the lesson type", example="Voluptas corporis repellendus tenetur. Provident et ea consequatur nesciunt deleniti ut. Dolor architecto minima et velit vitae ut. Expedita et vitae quia nesciunt porro. Nulla non nobis repellendus quo velit molestiae et."),
 * @OA\Property(property="createdAt", type="string", format="date-time", description="Creation timestamp", example="2023-10-28T12:00:00.000000Z"),
 * @OA\Property(property="updatedAt", type="string", format="date-time", description="Last update timestamp", example="2023-10-28T12:00:00.000000Z")
 * )
 */
class LessonTypeResource extends JsonResource
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
            'createdAt' => $this->created_at,
            'updatedAt' => $this->updated_at
        ];
    }
}
