<?php

namespace App\Http\Resources\Dashboard;

use Illuminate\Http\Resources\Json\JsonResource;
use Illuminate\Http\Request;

/**
 * @OA\Schema(
 * schema="LessonTrainerResource",
 * title="Lesson Trainer Resource",
 * description="Lesson Trainer model representation",
 * @OA\Property(property="id", type="integer", description="Lesson Trainer ID", example=1),
 * @OA\Property(property="lessonTypeId", type="integer", description="Lesson type ID of the lesson trainer", example=2),
 * @OA\Property(property="trainerId", type="integer", description="Trainer ID of the lesson trainer", example=2),
 * @OA\Property(property="createdAt", type="string", format="date-time", description="Creation timestamp", example="2023-10-28T12:00:00.000000Z"),
 * @OA\Property(property="updatedAt", type="string", format="date-time", description="Last update timestamp", example="2023-10-28T12:00:00.000000Z")
 * )
 */
class LessonTrainerResource extends JsonResource
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
            'lessonTypeId' => $this->lesson_type_id,
            'trainerId' => $this->trainer_id,
            'createdAt' => $this->created_at,
            'updatedAt' => $this->updated_at
        ];
    }
}
