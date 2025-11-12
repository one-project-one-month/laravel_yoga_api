<?php

namespace App\Http\Controllers\Dashboard;

use App\Http\Controllers\Controller;
use App\Http\Helpers\ApiResponse;
use App\Http\Resources\Dashboard\SubscriptionResource;
use App\Models\Subscription;
use Illuminate\Http\Request;
use Validator;

/**
 * @OA\Tag(
 * name="Subscriptions",
 * description="API Endpoints for managing subscriptions"
 * )
 */
class SubscriptionController extends Controller
{
    use ApiResponse;

    /**
     * @OA\Get(
     * path="/api/v1/subscriptions",
     * summary="Get a list of subscriptions",
     * description="Returns a paginated list of all subscriptions.",
     * tags={"Subscriptions"},
     * security={{"bearerAuth":{}}},
     * @OA\Response(
     * response=200,
     * description="Successful operation",
     * @OA\JsonContent(
     * type="object",
     * @OA\Property(property="message", type="string", example="Subscriptions retrieved successfully"),
     * @OA\Property(property="data", type="object",
     * @OA\Property(property="items", type="array", @OA\Items(ref="#/components/schemas/SubscriptionResource")),
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
        $subscriptions = Subscription::get();

        return $this->successResponse('Subscriptions retrieved successfully', SubscriptionResource::collection($subscriptions), 200);
    }

    /**
     * @OA\Post(
     * path="/api/v1/subscriptions",
     * summary="Create a new subscription",
     * description="Creates a new subscription.",
     * tags={"Subscriptions"},
     * security={{"bearerAuth":{}}},
     * @OA\RequestBody(
     * required=true,
     * @OA\JsonContent(
     * required={"name", "description", "price", "lessonTypeId", "duration"},
     * @OA\Property(property="name", type="string", example="Yoga"),
     * @OA\Property(property="description", type="text", example="Lorem, ipsum dolor sit amet consectetur adipisicing elit. Dolorem eos enim corrupti sunt minus nemo cum sint distinctio deserunt fugiat numquam molestiae, adipisci a dolore, fugit esse commodi earum. Quis?"),
     * @OA\Property(property="price", type="integer", example=350000),
     * @OA\Property(property="lessonTypeId", type="integer", example=2),
     * @OA\Property(property="duration", type="string", example="3 months"),
     * )
     * ),
     * @OA\Response(
     * response=201,
     * description="Subscription created successfully",
     * @OA\JsonContent(ref="#/components/schemas/SubscriptionResource")
     * ),
     * @OA\Response(response=422, description="Validation error"),
     * @OA\Response(response=500, description="Subscription creation failed"),
     * @OA\Response(response=401, description="Unauthenticated")
     * )
     */
    public function store(Request $request)
    {
        $validator = Validator::make($request->all(),
            [
                'name' => 'required|string|max:255',
                'description' => 'required|string',
                'price' => 'required|numeric|min:0',
                'lessonTypeId' => 'required|integer|exists:lesson_types,id',
                'duration' => 'required'
            ]);

        if ($validator->fails()) {
            return $this->errorResponse($validator->errors(), 422);
        }

        try {
            $subscription = Subscription::create([
                'name' => $request->name,
                'description' => $request->description,
                'price' => $request->price,
                'lesson_type_id' => $request->lesson_type_id,
                'duration' => $request->duration
            ]);

            return $this->successResponse('Subscription created successfully', SubscriptionResource::collection($subscription), 201);
        } catch (\Exception $e) {
            return $this->errorResponse('Subscription creation failed:' . $e->getMessage(), 500);
        }
    }

    /**
     * @OA\Put(
     * path="/api/v1/subscriptions/{id}",
     * summary="Update an existing subscription",
     * description="Updates the details of an existing subscription.",
     * tags={"Subscriptions"},
     * security={{"bearerAuth":{}}},
     * @OA\Parameter(
     * name="id",
     * in="path",
     * required=true,
     * description="ID of the subscription to update",
     * @OA\Schema(type="string")
     * ),
     * @OA\RequestBody(
     * required=true,
     * @OA\JsonContent(
     * required={"name", "description", "price", "lessonTypeId", "duration"},
     * @OA\Property(property="name", type="string", example="Yoga"),
     * @OA\Property(property="description", type="text", example="Lorem, ipsum dolor sit amet consectetur adipisicing elit. Dolorem eos enim corrupti sunt minus nemo cum sint distinctio deserunt fugiat numquam molestiae, adipisci a dolore, fugit esse commodi earum. Quis?"),
     * @OA\Property(property="price", type="integer", example=350000),
     * @OA\Property(property="lessonTypeId", type="integer", example=2),
     * @OA\Property(property="duration", type="string", example="3 months"),
     * )
     * ),
     * @OA\Response(
     * response=200,
     * description="Subscription Updated Successfully",
     * @OA\JsonContent(ref="#/components/schemas/SubscriptionResource")
     * ),
     * @OA\Response(response=422, description="Validation error"),
     * @OA\Response(response=404, description="Subscription not found"),
     * @OA\Response(response=401, description="Unauthenticated")
     * )
     */
    public function update(Request $request, string $id)
    {
        $validator = Validator::make($request->all(),
            [
                'name' => 'sometimes|string|max:255',
                'description' => 'sometimes|string',
                'price' => 'sometimes|numeric|min:0',
                'lesson_type_id' => 'sometimes|integer|exists:lesson_types,id',
                'duration' => 'sometimes'
            ]);

        if ($validator->fails()) {
            return $this->errorResponse($validator->errors(), 422);
        }

        try {
            $subscription = Subscription::findOrFail($id);
            $subscription->update($request->only(['name', 'description', 'price', 'lesson_type_id', 'duration']));

            return $this->successResponse('Subscription updated successfully', SubscriptionResource::collection($subscription), 200);
        } catch (\Exception $e) {
            return $this->errorResponse('Subscription update failed: ' . $e->getMessage(), 500);
        }
    }

    /**
     * @OA\Delete(
     *     path="/api/subscriptions/{id}",
     *     summary="Delete a specific subscription",
     *     description="This endpoint permanently deletes a subscription record from the system using its unique ID.",
     *     tags={"Subscriptions"},
     *     security={{"bearerAuth":{}}},
     *
     *
     *     @OA\Parameter(
     *         name="id",
     *         in="path",
     *         required=true,
     *         description="ID of the subscription to delete",
     *         @OA\Schema(type="integer", example=5)
     *     ),
     *
     *     @OA\Response(
     *         response=200,
     *         description="Subscription deleted successfully",
     *         @OA\JsonContent(
     *             @OA\Property(property="message", type="string", example="Subscription deleted successfully.")
     *         )
     *     ),
     *
     *     @OA\Response(
     *         response=404,
     *         description="Subscription not found",
     *         @OA\JsonContent(
     *             @OA\Property(property="message", type="string", example="Subscription not found.")
     *         )
     *     ),
     *
     *     @OA\Response(
     *         response=500,
     *         description="Internal server error",
     *         @OA\JsonContent(
     *             @OA\Property(property="message", type="string", example="Something went wrong while deleting subscription.")
     *         )
     *     )
     * )
     */
    public function destroy(string $id)
    {
        try {
            $subscription = Subscription::findOrFail($id);
            $subscription->delete();

            return $this->successResponse('Subscription deleted successfully', SubscriptionResource::collection($subscription), 200);
        } catch (\Exception $e) {
            return $this->errorResponse('Subscription delete failed: ' . $e->getMessage(), 500);
        }
    }
}
