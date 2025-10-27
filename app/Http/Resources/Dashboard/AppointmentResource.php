<?php

namespace App\Http\Resources\Dashboard;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class AppointmentResource extends JsonResource
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
            'member' => new UserResource($this->whenLoaded('member')),
            'adminId' => $this->admin_id,
            'admin' => new UserResource($this->whenLoaded('admin')),
            'trainerId' => $this->trainer_id,
            'trainer' => new UserResource($this->whenLoaded('trainer')),
            'appointmentDate' => $this->appointment_date,
            'appointmentFees' => $this->appointment_fees,
            'meetLink' => $this->meet_link,
            'isApproved' => $this->is_approved,
            'isCompleted' => $this->is_completed,
            'createdAt' => $this->created_at,
            'updatedAt' => $this->updated_at
        ];
    }
}
