<?php

namespace App\Http\Resources\Dashboard;


use Illuminate\Http\Request;
use App\Http\Resources\Dashboard\RoleResource;
use Illuminate\Http\Resources\Json\JsonResource;
use App\Http\Resources\Dashboard\TrainerResource;

class UserResource extends JsonResource
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
            'fullName' => $this->full_name,
            'nickName' => $this->nick_name,
            'email' => $this->email,
            'roleId' => $this->role_id,
            'role' => new RoleResource($this->whenLoaded('role')),
            'trainer' => new TrainerResource($this->whenLoaded('trainerDetails')),
            'profileUrl' => $this->profile_url,
            'profilePublicId' => $this->profile_public_id,
            'address' => $this->address,
            'phNoTelegram' => $this->ph_no_telegram,
            'phNoWhatsapp' => $this->ph_no_whatsapp,
            'dateOfBirth' => $this->date_of_birth,
            'placeOfBirth' => $this->place_of_birth,
            'weight' => $this->weight,
            'dailyRoutine' => $this->daily_routine_for_weekly,
            'specialRequest' => $this->special_request,
            'isVerified' => $this->is_verified,
            'isPremium' => $this->is_premium,
            'isFirstTimeAppointment' => $this->is_first_time_appointment,
            'startDate' => $this->start_date,
            'endDate' => $this->end_date,
            'createdAt' => $this->created_at,
            'updatedAt' => $this->updated_at
        ];
    }
}
