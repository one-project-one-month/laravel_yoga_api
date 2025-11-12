<?php

namespace App\Http\Controllers\Dashboard;

use App\Http\Controllers\Controller;
use App\Http\Helpers\ApiResponse;
use App\Http\Resources\Dashboard\AdminSubscriptionResource;
use App\Models\SubscriptionUser;
use Illuminate\Http\Request;

/**
 * @OA\Tag(
 * name="Admin Subscriptions",
 * description="API Endpoints for managing admin subscriptions"
 * )
 */
class AdminSubscriptionController extends Controller
{
    use ApiResponse;

    /**
     * @OA\Get(
     * path="/api/v1/subscriptions-users",
     * summary="Get a list of users's subscriptions",
     * description="Returns a paginated list of all users's subscriptions.",
     * tags={"Admin Subscriptions"},
     * security={{"bearerAuth":{}}},
     * @OA\Response(
     * response=200,
     * description="Successful operation",
     * @OA\JsonContent(
     * type="object",
     * @OA\Property(property="message", type="string", example="Users's subscriptions retrieved successfully"),
     * @OA\Property(property="data", type="object",
     * @OA\Property(property="items", type="array", @OA\Items(ref="#/components/schemas/AdminSubscriptionResource")),
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
        $subUser = SubscriptionUser::with(['user', 'subscription'])
            ->paginate(config('pagination.perPage'));

        return $this->successResponse('Successfully', $this->buildPaginatedResourceResponse(AdminSubscriptionResource::class, $subUser), 200);
    }

    /**
     * GET /api/v1/subscription-users/{id}
     * Show subscription user information
     */
    public function show($id)
    {
        $subUser = SubscriptionUser::find($id);

        if (!$subUser) {
            return $this->errorResponse('Subscription not found', 404);
        }

        return $this->successResponse('Successfully', new AdminSubscriptionResource($subUser), 200);
    }

    /**
     * @OA\Put(
     * path="/api/v1/subscription-users/{id}",
     * summary="Update an existing user's subscription",
     * description="Updates the details of an existing user's subscription.",
     * tags={"Admin Subscriptions"},
     * security={{"bearerAuth":{}}},
     * @OA\Parameter(
     * name="id",
     * in="path",
     * required=true,
     * description="ID of the user's subscription to update",
     * @OA\Schema(type="string")
     * ),
     * @OA\RequestBody(
     * required=true,
     * @OA\JsonContent(
     * required={"status"},
     * @OA\Property(property="status", type="string", example="accept"),
     * )
     * ),
     * @OA\Response(
     * response=200,
     * description="User's subscription Updated Successfully",
     * @OA\JsonContent(ref="#/components/schemas/AdminSubscriptionResource")
     * ),
     * @OA\Response(response=422, description="Validation error"),
     * @OA\Response(response=404, description="User's subscription not found"),
     * @OA\Response(response=401, description="Unauthenticated")
     * )
     */
    public function update(Request $request, $id)
    {
        $subUser = SubscriptionUser::with('subscriptions')->findOrFail($id);

        if (!$subUser) {
            return $this->errorResponse('Record not found!', 404);
        }

        $subDuration = $subUser->subscription->duration;

        if ($request->status == 'accept') {
            $subUser->update([
                'status' => 'accept',
                'start_date' => now(),
                'end_date' => now()->addMonths($subDuration),
            ]);
        } else if ($request->status == 'reject') {
            $subUser->update(['status' => 'reject']);
        } else {
            return $this->errorResponse('Invalid status value', 422);
        }

        return $this->successResponse('Status updated!', new AdminSubscriptionResource($subUser), 200);
    }
}
