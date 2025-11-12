<?php

namespace App\Http\Resources\Dashboard;

use Illuminate\Http\Resources\Json\JsonResource;
use Illuminate\Http\Request;

/**
 * @OA\Schema(
 * schema="TrainerResource",
 * title="Trainer Resource",
 * description="Trainer model representation",
 * @OA\Property(property="id", type="integer", description="Trainer ID", example=1),
 * @OA\Property(property="userId", type="integer", description="User ID of the trainer", example=9),
 * @OA\Property(property="bio", type="text", description="Bio of the trainer", example="Lorem ipsum dolor sit amet consectetur adipisicing elit. Inventore, quae!"),
 * @OA\Property(property="universityName", type="string", description="University of the trainer", example="Yangon University"),
 * @OA\Property(property="degree", type="string", description="Degree of the trainer", example="BSc(Physics)"),
 * @OA\Property(property="startDate", type="date", description="University start date of the trainer", example="01-01-20xx"),
 * @OA\Property(property="endDate", type="date", description="University end date of the trainer", example="01-01-20xx"),
 * @OA\Property(property="salary", type="decimal", description="Salary of the trainer", example=250000.50),
 * @OA\Property(property="branchLocation", type="string", description="Branch location of the trainer", example="Yangon"),
 * @OA\Property(property="createdAt", type="string", format="date-time", description="Creation timestamp", example="2023-10-28T12:00:00.000000Z"),
 * @OA\Property(property="updatedAt", type="string", format="date-time", description="Last update timestamp", example="2023-10-28T12:00:00.000000Z")
 * )
 */
class TrainerResource extends JsonResource
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
            'trainerId' => $this->trainer_id,
            'trainer' => new UserResource($this->whenLoaded('trainer')),
            'bio' => $this->bio,
            'universityName' => $this->university_name,
            'degree' => $this->degree,
            'city' => $this->city,
            'startDate' => $this->start_date,
            'endDate' => $this->end_date,
            'salary' => $this->salary,
            'branchLocation' => $this->branch_location,
            'createdAt' => $this->created_at,
            'updatedAt' => $this->updated_at
        ];
    }
}
