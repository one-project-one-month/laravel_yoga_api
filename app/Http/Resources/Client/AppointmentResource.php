<?php

namespace App\Http\Resources\Client;

use App\Http\Resources\Dashboard\UserResource;
use Illuminate\Http\Resources\Json\JsonResource;
use Illuminate\Http\Request;

/**
 * @OA\Schema(
 * schema="ClientAppointmentResource",
 * title="Client Appointment Resource",
 * description="Client Appointment model representation",
 * @OA\Property(property="id", type="integer", description="Appointment ID", example=1),
 * @OA\Property(property="userId", type="integer", description="User ID of the appointment", example=3),
 * @OA\Property(property="user", ref="#/components/schemas/UserResource"),
 * @OA\Property(property="appointmentDate", type="date", description="Date of appointment", example="01-01-20xx"),
 * @OA\Property(property="appointmentTime", type="time", description="Time of appointment", example="08:00"),
 * @OA\Property(property="appointmentType", type="string", description="Type of appointment", example="Healing"),
 * )
 */
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
        ];
    }
}
