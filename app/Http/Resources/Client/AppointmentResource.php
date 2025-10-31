<?php

namespace App\Http\Resources\Client;

use App\Http\Resources\Dashboard\UserResource;
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
            'user' => new UserResource($this->whenLoaded('user')),
            'appointmentDate' => $this->appointment_date,
            'appointmentTime' => $this->appointment_time,
            'appointmentType' => $this->appointment_type,
            'appointmentFees' => $this->appointment_fees,
            'meetLink' => $this->meet_link,
            'isApproved' => $this->is_approved,
            'isCompleted' => $this->is_completed
        ];
    }
}
