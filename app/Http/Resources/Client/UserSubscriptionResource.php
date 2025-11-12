<?php

namespace App\Http\Resources\Client;

use Illuminate\Http\Resources\Json\JsonResource;
use Illuminate\Http\Request;

/**
 * @OA\Schema(
 * schema="ClientSubscriptionResource",
 * title="Client Subscription Resource",
 * description="Client SubscriptionUsers model representation",
 * @OA\Property(property="id", type="integer", description="User Subscription ID", example=1),
 * @OA\Property(property="userId", type="integer", description="User ID of the subscription", example=3),
 * @OA\Property(property="user", ref="#/components/schemas/UserResource"),
 * @OA\Property(property="subscriptionId", type="integer", description="Subscription ID", example="1"),
 * @OA\Property(property="subscription", ref="#/components/schemas/SubscriptionResource"),
 * @OA\Property(property="status", type="string", description="Wait for admin approve", example="pending"),
 * @OA\Property(property="startDate", type="date", description="Start date for user's subscription", example="01-01-20xx"),
 * @OA\Property(property="endDate", type="date", description="End date for user's subscription", example="01-01-20xx"),
 * @OA\Property(property="createdAt", type="string", format="date-time", description="Creation timestamp", example="2023-10-28T12:00:00.000000Z"),
 * @OA\Property(property="updatedAt", type="string", format="date-time", description="Last update timestamp", example="2023-10-28T12:00:00.000000Z")
 * )
 */
class UserSubscriptionResource extends JsonResource
{
    /**
     * Transform the resource into an array.
     *
     * @return array<string, mixed>
     */
    public function toArray(Request $request): array
    {
        return [
            'userId' => $this->user_id,
            'subscriptionId' => $this->subscription_id,
            'paymentMethodId' => $this->payment_method_id,
            'phNo' => $this->ph_no,
            'transactionId' => $this->transaction_id,
            'payslipImageUrl' => $this->payslip_image_url,
            'payslipImagePublicId' => $this->payslip_image_public_id,
            'status' => $this->status
        ];
    }
}
