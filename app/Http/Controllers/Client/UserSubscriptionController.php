<?php

namespace App\Http\Controllers\Client;

use App\Http\Controllers\Controller;
use App\Http\Helpers\ApiResponse;
use App\Http\Resources\Client\UserSubscriptionResource;
use App\Http\Resources\Dashboard\AdminSubscriptionResource;
use App\Models\PaymentHistory;
use App\Models\Subscription;
use App\Models\SubscriptionUser;
use CloudinaryLabs\CloudinaryLaravel\Facades\Cloudinary;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;

/**
 * @OA\Tag(
 * name="Client Subscriptions",
 * description="API Endpoints for managing client subscriptions"
 * )
 */
class UserSubscriptionController extends Controller
{
    use ApiResponse;

    /**
     * @OA\Get(
     * path="/api/v1/users/{id}/subscriptions",
     * summary="Get a list of user's subscriptions",
     * description="Returns a paginated list of a user's subscriptions.",
     * tags={"Client Subscriptions"},
     * security={{"bearerAuth":{}}},
     * @OA\Parameter(
     * name="id",
     * in="path",
     * required=true,
     * description="ID of the user whose subscriptions are being fetched",
     * @OA\Schema(
     * type="integer",
     * format="int64",
     * example=1
     * )
     * ),
     * @OA\Response(
     * response=200,
     * description="Successful operation",
     * @OA\JsonContent(
     * type="object",
     * @OA\Property(property="message", type="string", example="User's subscriptions retrieved successfully"),
     * @OA\Property(property="data", type="object",
     * @OA\Property(
     * property="items",
     * type="array",
     * description="Array of client subscription resources",
     * @OA\Items(ref="#/components/schemas/ClientSubscriptionResource")
     * ),
     * @OA\Property(property="pagination", type="object",
     * @OA\Property(property="total", type="integer", description="Total number of items", example=50),
     * @OA\Property(property="per_page", type="integer", description="Items per page", example=15),
     * @OA\Property(property="current_page", type="integer", description="Current page number", example=1),
     * @OA\Property(property="last_page", type="integer", description="Last page number", example=4)
     * )
     * )
     * )
     * ),
     * @OA\Response(
     * response=401,
     * description="Unauthenticated",
     * @OA\JsonContent(
     * @OA\Property(property="message", type="string", example="Unauthenticated.")
     * )
     * )
     * )
     */
    public function index($id)
    {
        $subscription = SubscriptionUser::where('user_id', $id)->get();

        if (!$subscription) {
            return $this->errorResponse('Subscription not found', 404);
        }

        return $this->successResponse('Your subscription ', UserSubscriptionResource::collection($subscription), 200);
    }

    /**
     * @OA\Post(
     * path="/api/v1/users/{id}/{subscriptionId}/subscriptions",
     * summary="Create user's subscription",
     * description="Create user's subscription.",
     * tags={"Client Subscriptions"},
     * security={{"bearerAuth":{}}},
     * @OA\Parameter(
     * name="id",
     * in="path",
     * required=true,
     * description="ID of the user",
     * @OA\Schema(type="string")
     * ),
     * @OA\Parameter(
     * name="subscriptionId",
     * in="path",
     * required=true,
     * description="ID of the subscription",
     * @OA\Schema(type="string")
     * ),
     * @OA\RequestBody(
     * required=true,
     * @OA\JsonContent(
     * required={"phoneNo", "payslipImage", "transanctionId"},
     * @OA\Property(property="phoneNo", type="string", example="09xxxxxxxx"),
     * @OA\Property(property="payslipImage", type="string", example="payslipexample.jpg"),
     * @OA\Property(property="transanctionId", type="string", example="9876541230"),
     * )
     * ),
     * @OA\Response(
     * response=200,
     * description="User's subscription Created Successfully",
     * @OA\JsonContent(ref="#/components/schemas/ClientSubscriptionResource")
     * ),
     * @OA\Response(response=422, description="Validation error"),
     * @OA\Response(response=401, description="Unauthenticated")
     * )
     */
    public function store(Request $request, $id, $subscriptionId)
    {
        $validator = Validator::make($request->all(), [
            'phoneNo' => 'required',
            'payslipImage' => 'required|mimes:png,jpg,jpeg',
            'transanctionId' => 'required',
        ]);

        if ($validator->fails()) {
            return $this->errorResponse($validator->errors(), 422);
        }

        if (!$subscription) {
            return $this->errorResponse('Subscription not found!', 404);
        }

        try {
            $paymentData = [
                'user_id' => $id,
                'payment_method_id' => $request->paymentMethodId,
                'subscription_id' => $subscriptionId,
                'ph_no' => $request->phoneNo,
                'transaction_id' => $request->transactionId,
            ];

            if ($request->hasFile('payslipImage')) {
                $uploadedFile = Cloudinary::upload($request->file('payslipImage')->getRealPath(), ['folder' => 'payslip_image'])->getSecurePath();
                $paymentData['payslip_image_url'] = $uploadedFile['secure_url'];
                $paymentData['payslip_image_public_id'] = $uploadedFile['public_id'];
            }

            PaymentHistory::create($paymentData);

            $user = SubscriptionUser::create([
                'user_id' => $id,
                'subscription_id' => $subscriptionId,
                'status' => 'pending'
            ]);

            return $this->successResponse(
                'Payment submitted.Waiting for admin approval.',
                [
                    'payment' => new UserSubscriptionResource($paymentData),
                    'subscription' => new AdminSubscriptionResource($user)
                ],
                201
            );
        } catch (\Exception $e) {
            return $this->errorResponse('Subscription attach failed: ' . $e->getMessage(), 500);
        }
    }
}
