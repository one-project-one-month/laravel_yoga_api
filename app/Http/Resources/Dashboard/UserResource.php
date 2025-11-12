<?php

namespace App\Http\Resources\Dashboard;

use App\Http\Resources\Dashboard\RoleResource;
use App\Http\Resources\Dashboard\TrainerResource;
use Illuminate\Http\Resources\Json\JsonResource;
use Illuminate\Http\Request;

/**
 * @OA\Schema(
 * schema="UserResource",
 * title="User Resource",
 * description="User model representation",
 * @OA\Property(property="id", type="integer", description="User ID", example=1),
 * @OA\Property(property="Fullname", type="string", description="Fullname of the user", example="John Doe"),
 * @OA\Property(property="Nickname", type="string", description="Nickname of the user", example="John"),
 * @OA\Property(property="email", type="string", format="email", description="Email of the user", example="johndoe@example.com"),
 * @OA\Property(property="profileUrl", type="string", description="Profile url of the user", example="example.jpg"),
 * @OA\Property(property="profilePublicId", type="string", description="Profile Id of the user", example="1"),
 * @OA\Property(property="address", type="string", description="Profile Id of the user", example="Rose Mary Street"),
 * @OA\Property(property="TelegramPh", type="string", description="Telegram number of the user", example="09xxxxxxx"),
 * @OA\Property(property="WhatsappPh", type="string", description="Whatsapp number of the user", example="09xxxxxxx"),
 * @OA\Property(property="dateOfBirth", type="string", description="Birthday of the user", example="10-01-19xx"),
 * @OA\Property(property="placeOfBirth", type="string", description="Birth place of the user", example="Yangon"),
 * @OA\Property(property="weight", type="integer", description="weight of the user", example="56"),
 * @OA\Property(property="dailyRoutine", type="text", description="Daily routine of the user", example="Lorem ipsum dolor sit amet consectetur adipisicing elit. Perferendis beatae reiciendis quia iure id debitis, accusantium nisi et fugit alias temporibus aspernatur pariatur obcaecati sint. Ullam quae nostrum debitis eum"),
 * @OA\Property(property="specialRequest", type="text", description="Special request of the user", example="Lorem"),
 * @OA\Property(property="isVerified", type="boolean", description="Email verified of the user", example="true"),
 * @OA\Property(property="isPremium", type="boolean", description="Premium of the user", example="false"),
 * @OA\Property(property="startDate", type="date", description="Premium start date of the user", example="01-01-20xx"),
 * @OA\Property(property="endDate", type="date", description="Premium end date of the user", example="01-01-20xx"),
 * @OA\Property(property="createdAt", type="string", format="date-time", description="Creation timestamp", example="2023-10-28T12:00:00.000000Z"),
 * @OA\Property(property="updatedAt", type="string", format="date-time", description="Last update timestamp", example="2023-10-28T12:00:00.000000Z")
 * )
 */
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
