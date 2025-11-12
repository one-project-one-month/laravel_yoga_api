<?php

namespace App\Http\Resources\Dashboard;

use Illuminate\Http\Resources\Json\JsonResource;
use Illuminate\Http\Request;

/**
 * @OA\Schema(
 * schema="LessonResource",
 * title="Lesson Resource",
 * description="Lesson model representation",
 * @OA\Property(property="id", type="integer", description="Lesson ID", example=1),
 * @OA\Property(property="title", type="string", description="Title of the lesson", example="Reprehenderit accusamus."),
 * @OA\Property(property="slug", type="string", description="Slug of the lesson", example="blanditiis-aliquam-et-debitis-ex-totam-praesentium-exercitationem"),
 * @OA\Property(property="description", type="text", description="Description of the lesson", example="jVelit autem fuga distinctio autem atque quaerat. Maiores eius recusandae maxime illum quibusdam animi inventore. Quia voluptate voluptates doloremque aperiam labore quos numquam. Rerum autem velit tempore illo quia ut. Sit doloribus voluptates nisi tempore."),
 * @OA\Property(property="level", type="string", description="Level of the lesson", example="beginner"),
 * @OA\Property(property="videoUrl", type="string", description="Video Url of the lesson", example="examplevideo.mp4"),
 * @OA\Property(property="videoPublicId", type="string", description="Video Id of the lesson", example="1"),
 * @OA\Property(property="durationMinutes", type="integer", description="Duration minutes of the lesson", example="45"),
 * @OA\Property(property="isFree", type="boolean", description="Free video of the lesson", example="true"),
 * @OA\Property(property="isPremium", type="boolean", description="Premium video of the lesson", example="false"),
 * @OA\Property(property="trainerId", type="integer", description="Trainer's of the lesson", example="5"),
 * @OA\Property(property="createdAt", type="string", format="date-time", description="Creation timestamp", example="2023-10-28T12:00:00.000000Z"),
 * @OA\Property(property="updatedAt", type="string", format="date-time", description="Last update timestamp", example="2023-10-28T12:00:00.000000Z")
 * )
 */
class LessonResource extends JsonResource
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
            'title' => $this->title,
            'slug' => $this->slug,
            'description' => $this->description,
            'level' => $this->level,
            'videoUrl' => $this->video_url,
            'videoPublicId' => $this->video_public_id,
            'lessonTypeId' => $this->lesson_type_id,
            'durationMinutes' => $this->duration_minutes,
            'isFree' => $this->is_free,
            'isPremium' => $this->is_premium,
            'trainerId' => $this->trainer_id,
            'createdAt' => $this->created_at,
            'updatedAt' => $this->updated_at
        ];
    }
}
