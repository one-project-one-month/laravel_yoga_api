<?php

namespace App\Http\Controllers\Client;

use Illuminate\Http\Request;
use App\Models\SubscriptionUser;
use App\Http\Helpers\ApiResponse;
use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\Validator;
use App\Http\Resources\Dashboard\SubscriptionUserResource;

class SubscriptionUserController extends Controller
{
    use ApiResponse;

    public function index() {
        $subUser = SubscriptionUser::all();

        return $this->successResponse('Successfully', SubscriptionUserResource::collection($subUser), 200);
    }

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

        return $this->successResponse('Status updated!', new SubscriptionUserResource($subUser), 200);
    }
}
