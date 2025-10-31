<?php

namespace App\Http\Controllers\Dashboard;

use Illuminate\Http\Request;
use App\Models\SubscriptionUser;
use App\Http\Helpers\ApiResponse;
use App\Http\Controllers\Controller;
use App\Http\Resources\Dashboard\AdminSubscriptionResource;

class AdminSubscriptionController extends Controller
{
    use ApiResponse;

    /**
     * GET /api/v1/subscription-users
     * List all subscription users
     */
    public function index() {
        $subUser = SubscriptionUser::with(['user', 'subscription'])
                ->paginate(config('pagnation.perPage'));

        return $this->successResponse('Successfully', $this->buildPaginatedResourceResponse(AdminSubscriptionResource::class, $subUser), 200);
    }

    /**
     * GET /api/v1/subscription-users/{id}
     * Show subscription user information
     */
    public function show($id) {
        $subUser = SubscriptionUser::find($id);

        if(!$subUser) {
            return $this->errorResponse('Subscription not found', 404);
        }

        return $this->successResponse('Successfully', new AdminSubscriptionResource($subUser), 200);
    }

    /**
     * PUT /api/v1/subscription-users/{id}
     * Update subscription user information
     */
    public function update(Request $request, $id)
    {
        $subUser = SubscriptionUser::with('subscriptions')->findOrFail($id);

        if(!$subUser) {
            return $this->errorResponse('Record not found!', 404);
        }

        $subDuration = $subUser->subscription->duration;

        if($request->status == 'accept') {
            $subUser->update([
                'status' => 'accept',
                'start_date' => now(),
                'end_date' => now()->addMonths($subDuration),
            ]);
        } else if($request->status == 'reject') {
            $subUser->update(['status' => 'reject']);
        } else {
            return $this->errorResponse('Invalid status value', 422);
        }

        return $this->successResponse('Status updated!', new AdminSubscriptionResource($subUser), 200);
    }
}
