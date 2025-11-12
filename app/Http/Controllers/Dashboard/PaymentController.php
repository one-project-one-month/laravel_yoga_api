<?php

namespace App\Http\Controllers\Dashboard;

use App\Http\Controllers\Controller;
use App\Http\Helpers\ApiResponse;
use App\Http\Resources\Dashboard\PaymentResource;
use App\Models\Payment;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Http\Request;
use Illuminate\Notifications\Notifiable;
use Illuminate\Support\Facades\Validator;
use Laravel\Sanctum\HasApiTokens;

/**
 * @OA\Tag(
 * name="Payments",
 * description="API Endpoints for managing payments"
 * )
 */
class PaymentController extends Controller
{
    use ApiResponse, HasApiTokens, HasFactory, Notifiable;

    /**
     * @OA\Post(
     * path="/api/v1/payments",
     * summary="Create a new payment account.",
     * description="Creates a new payment account.",
     * tags={"Payments"},
     * security={{"bearerAuth":{}}},
     * @OA\RequestBody(
     * required=true,
     * @OA\JsonContent(
     * required={"accountName", "accountNumber", "accountType"},
     * @OA\Property(property="accountName", type="string", example="Alice Doe"),
     * @OA\Property(property="accountNumber", type="string", example="12345678900"),
     * @OA\Property(property="accountType", type="string", example="Hello Pay"),
     *
     * )
     * ),
     * @OA\Response(
     * response=201,
     * description="Payment created successfully",
     * @OA\JsonContent(ref="#/components/schemas/PaymentResource")
     * ),
     * @OA\Response(response=422, description="Validation error"),
     * @OA\Response(response=500, description="Payment creation failed"),
     * @OA\Response(response=401, description="Unauthenticated")
     * )
     */
    public function store(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'accountName' => 'required|string|max:255',
            'accountNumber' => 'required',
            'accountType' => 'required|string|max:255'
        ]);

        if ($validator->fails()) {
            return $this->errorResponse($validator->errors(), 422);
        }

        try {
            $payment = Payment::create([
                'account_name' => $request->accountName,
                'account_number' => $request->accountNumber,
                'account_type' => $request->accountType
            ]);

            return $this->successResponse('Payment created sucessfully', new PaymentResource($payment), 201);
        } catch (\Exception $e) {
            return $this->errorResponse('Payment creation failed:' . $e->getMessage(), 500);
        }
    }

    /**
     * @OA\Get(
     * path="/api/v1/payments",
     * summary="Get a list of payments",
     * description="Returns a paginated list of all payments.",
     * tags={"Payments"},
     * security={{"bearerAuth":{}}},
     * @OA\Response(
     * response=200,
     * description="Successful operation",
     * @OA\JsonContent(
     * type="object",
     * @OA\Property(property="message", type="string", example="Payments retrieved successfully"),
     * @OA\Property(property="data", type="object",
     * @OA\Property(property="items", type="array", @OA\Items(ref="#/components/schemas/PaymentResource")),
     * @OA\Property(property="pagination", type="object",
     * @OA\Property(property="total", type="integer", example=50),
     * @OA\Property(property="per_page", type="integer", example=15),
     * @OA\Property(property="current_page", type="integer", example=1),
     * @OA\Property(property="last_page", type="integer", example=4)
     * )
     * )
     * )
     * ),
     * @OA\Response(response=401, description="Unauthenticated")
     * )
     */
    public function index()
    {
        $payment = Payment::all();

        return $this->successResponse('Payment retrieved sucessfully', PaymentResource::collection($payment), 200);
    }

    /**
     * @OA\Put(
     * path="/api/v1/payments/{id}",
     * summary="Update an existing payment",
     * description="Updates the details of an existing payment.",
     * tags={"Payments"},
     * security={{"bearerAuth":{}}},
     * @OA\Parameter(
     * name="id",
     * in="path",
     * required=true,
     * description="ID of the payment to update",
     * @OA\Schema(type="string")
     * ),
     * @OA\RequestBody(
     * required=true,
     * @OA\JsonContent(
     * required={"accountName", "accountNumber", "accountType"},
     * @OA\Property(property="accountName", type="string", example="Alice Doe"),
     * @OA\Property(property="accountNumber", type="string", example="12345678900"),
     * @OA\Property(property="accountType", type="string", example="Hello Pay"),
     * )
     * ),
     * @OA\Response(
     * response=200,
     * description="Payment Updated Successfully",
     * @OA\JsonContent(ref="#/components/schemas/PaymentResource")
     * ),
     * @OA\Response(response=422, description="Validation error"),
     * @OA\Response(response=404, description="Payment not found"),
     * @OA\Response(response=401, description="Unauthenticated")
     * )
     */
    public function update(Request $request, $id)
    {
        $paymentData = Payment::findOrFail($id);

        if (!$paymentData) {
            return $this->errorResponse('Payment not found', 404);
        }

        $validator = Validator::make($request->all(), [
            'accountName' => 'required|string|max:255',
            'accountNumber' => 'required',
            'accountType' => 'required|string|max:255'
        ]);

        if ($validator->fails()) {
            return $this->errorResponse($validator->errors(), 422);
        }

        try {
            $payment = Payment::update([
                'account_name' => $request->accountName,
                'account_number' => $request->accountNumber,
                'account_type' => $request->accountType
            ]);

            return $this->successResponse('Payment updated sucessfully', new PaymentResource($payment), 200);
        } catch (\Exception $e) {
            return $this->errorResponse('Payment updated failed:' . $e->getMessage(), 500);
        }
    }

    /**
     * @OA\Delete(
     *     path="/api/payments/{id}",
     *     summary="Delete a specific payment",
     *     description="This endpoint permanently deletes a payment record from the system using its unique ID.",
     *     tags={"Payments"},
     *     security={{"bearerAuth":{}}},
     *
     *     @OA\Parameter(
     *         name="id",
     *         in="path",
     *         required=true,
     *         description="ID of the payment to delete",
     *         @OA\Schema(type="integer", example=5)
     *     ),
     *
     *     @OA\Response(
     *         response=204,
     *         description="Payment deleted successfully",
     *         @OA\JsonContent(
     *             @OA\Property(property="message", type="string", example="Payment deleted successfully.")
     *         )
     *     ),
     *
     *     @OA\Response(
     *         response=404,
     *         description="Payment not found",
     *         @OA\JsonContent(
     *             @OA\Property(property="message", type="string", example="Payment not found.")
     *         )
     *     ),
     *
     *     @OA\Response(
     *         response=500,
     *         description="Internal server error",
     *         @OA\JsonContent(
     *             @OA\Property(property="message", type="string", example="Something went wrong while deleting payment.")
     *         )
     *     )
     * )
     */
    public function destroy($id)
    {
        $payment = Payment::findOrFail($id);

        if (!$payment) {
            return $this->errorResponse('Payment not found', 404);
        }

        $payment->delete();

        return $this->successResponse('Payment deleted successfully', '', 204);
    }
}
