<?php

namespace App\Http\Resources\Dashboard;

use Illuminate\Http\Resources\Json\JsonResource;
use Illuminate\Http\Request;

/**
 * @OA\Schema(
 * schema="TestimonialResource",
 * title="Testimonial Resource",
 * description="Testimonial model representation",
 * @OA\Property(property="id", type="integer", description="Testimonial ID", example=1),
 * @OA\Property(property="userId", type="integer", description="User of the testimonial", example=1),
 * @OA\Property(property="user", ref="#/components/schemas/UserResource"),
 * @OA\Property(property="comment", type="text", description="Comment review of page", example="Minima id quidem eius reiciendis hic aut expedita velit quod et aut molestias."),
 * @OA\Property(property="createdAt", type="string", format="date-time", description="Creation timestamp", example="2023-10-28T12:00:00.000000Z"),
 * @OA\Property(property="updatedAt", type="string", format="date-time", description="Last update timestamp", example="2023-10-28T12:00:00.000000Z")
 * )
 */
class TestimonialResource extends JsonResource
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
            'comment' => $this->comment,
            'createdAt' => $this->created_at,
            'updatedAt' => $this->updated_at
        ];
    }
}
