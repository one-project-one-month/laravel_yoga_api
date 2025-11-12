<?php

namespace App\Http\Resources\Dashboard;

use Illuminate\Http\Resources\Json\JsonResource;
use Illuminate\Http\Request;

/**
 * @OA\Schema(
 * schema="PaymentResource",
 * title="Payment Resource",
 * description="Payment model representation",
 * @OA\Property(property="id", type="integer", description="Payment ID", example=1),
 * @OA\Property(property="accountName", type="string", description="Account name of the payment", example="Alice Doe"),
 * @OA\Property(property="accountNumber", type="string", description="Account number of the payment", example="12345678900"),
 * @OA\Property(property="accountType", type="string", description="Account type of the payment", example="Hello Pay"),
 * @OA\Property(property="createdAt", type="string", format="date-time", description="Creation timestamp", example="2023-10-28T12:00:00.000000Z"),
 * @OA\Property(property="updatedAt", type="string", format="date-time", description="Last update timestamp", example="2023-10-28T12:00:00.000000Z")
 * )
 */
class PaymentResource extends JsonResource
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
            'accountName' => $this->account_name,
            'accountNumber' => $this->account_number,
            'accountType' => $this->account_type,
            'createdAt' => $this->created_at,
            'updatedAt' => $this->updated_at
        ];
    }
}
