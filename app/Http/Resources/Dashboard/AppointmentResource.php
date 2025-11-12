<?php

namespace App\Http\Resources\Dashboard;

use Illuminate\Http\Resources\Json\JsonResource;
use Illuminate\Http\Request;

/**
 * @OA\Schema(
 * schema="AppointmentResource",
 * title="Appointment Resource",
 * description="Appointment model representation",
 * @OA\Property(property="id", type="integer", description="Appointment ID", example=1),
 * @OA\Property(property="userId", type="integer", description="User ID of the appointment", example=3),
 * @OA\Property(property="user", ref="#/components/schemas/UserResource"),
 * @OA\Property(property="appointmentDate", type="date", description="Date of appointment", example="01-01-20xx"),
 * @OA\Property(property="appointmentTime", type="time", description="Time of appointment", example="08:00"),
 * @OA\Property(property="appointmentFees", type="decimal", description="Fees of appointment", example=10000.00),
 * @OA\Property(property="meetLink", type="string", description="Meet link of appointment", example="kdifwu-fkes-ked"),
 * @OA\Property(property="isApproved", type="string", description="Approve of appointment", example="accept"),
 * @OA\Property(property="isCompleted", type="boolean", description="Completed of appointment", example=false),
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
            'appointmentFees' => $this->appointment_fees,
            'meetLink' => $this->meet_link,
            'isApproved' => $this->is_approved,
            'isCompleted' => $this->is_completed,
        ];
    }
}
