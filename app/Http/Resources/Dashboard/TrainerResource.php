<?php

namespace App\Http\Resources\Dashboard;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

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
