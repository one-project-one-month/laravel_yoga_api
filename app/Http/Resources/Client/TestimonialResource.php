<?php

namespace App\Http\Resources\Client;

use App\Http\Resources\Dashboard\UserResource;
use Illuminate\Http\Resources\Json\JsonResource;
use Illuminate\Http\Request;

/**
 * @OA\Schema(
 *     schema="ClientTestimonialResource",
 *     title="Client Teatimonial Resource",
 *     description="Client-facing Testimonial model representation",
 *     @OA\Property(
 *         property="id",
 *         type="integer",
 *         example=1
 *     ),
 *     @OA\Property(
 *         property="userId",
 *         type="integer",
 *         example=1
 *     ),
 *     @OA\Property(
 *         property="user",
 *         ref="#/components/schemas/UserResource"
 *     ),
 *     @OA\Property(
 *         property="comment",
 *         type="string",
 *         example="Minima id quidem eius reiciendis hic aut expedita velit quod et aut molestias."
 *     ),
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
            'comment' => $this->comment
        ];
    }
}
