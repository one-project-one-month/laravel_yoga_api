<?php

namespace App\Http\Resources\Client;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class UserPaymentResource extends JsonResource
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
