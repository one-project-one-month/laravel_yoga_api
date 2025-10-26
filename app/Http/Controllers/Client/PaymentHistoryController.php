<?php

namespace App\Http\Controllers\Client;


use App\Http\Resources\Dashboard\SubscriptionUserResource;
use App\Models\Subscription;
use Illuminate\Http\Request;
use App\Models\PaymentHistory;
use App\Models\SubscriptionUser;
use App\Http\Helpers\ApiResponse;
use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\Validator;
use App\Http\Resources\Client\UserPaymentResource;
use CloudinaryLabs\CloudinaryLaravel\Facades\Cloudinary;

class PaymentHistoryController extends Controller
{
    use ApiResponse;

    public function store(Request $request)
    {

        $validator = Validator::make($request->all(), [
            'phoneNo' => 'required',
            'payslipImage' => 'required|mimes:png,jpg,jpeg',
            'transanctionId' => 'required',
            'subscriptionId' => 'required|integer|exists:subscriptions,id',
        ]);

        if ($validator->fails()) {
            return $this->errorResponse($validator->errors(), 422);
        }

        $user = auth()->user();
        $subscription = Subscription::find($request->subscriptionId);

        if (!$subscription) {
            return $this->errorResponse('Subscription not found!', 404);
        }

        try {
            $paymentData = [
                'user_id' => $request->userId,
                'payment_method_id' => $request->paymentMethodId,
                'subscription_id' => $request->subscriptionId,
                'ph_no' => $request->phNo,
                'transaction_id' => $request->transactionId,
            ];

            if ($request->hasFile('payslipImage')) {
                $uploadedFile = Cloudinary::upload($request->file('payslipImage')->getRealPath(), ['folder' => 'payslip_image'])->getSecurePath();
                $paymentData['payslip_image_url'] = $uploadedFile['secure_url'];
                $paymentData['payslip_image_public_id'] = $uploadedFile['public_id'];
            }

            PaymentHistory::create($paymentData);

            $user = SubscriptionUser::create([
                'user_id' => $request->userId,
                'subscription_id' => $request->subscriptionId,
                'status' => 'pending'
            ]);

            return $this->successResponse(
                'Payment submitted.Waiting for admin approval.',
                [
                    "payment" => new UserPaymentResource($paymentData),
                    "subscription" => new SubscriptionUserResource($user)
                ],
                201
            );

        } catch (\Exception $e) {
            return $this->errorResponse('Subscription attach failed: ' . $e->getMessage(), 500);
        }
    }

    public function show($id)
    {
        $subUser = SubscriptionUser::findOrFail($id);

        if (!$subUser) {
            return $this->errorResponse('Subscription not found!', 404);
        }

        return $this->successResponse('', new SubscriptionUserResource($subUser), 200);
    }
}
