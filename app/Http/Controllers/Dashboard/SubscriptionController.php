<?php

namespace App\Http\Controllers\Dashboard;

use App\Http\Controllers\Controller;
use App\Http\Helpers\ApiResponse;
use App\Http\Resources\Dashboard\SubscriptionResource;
use App\Models\Subscription;
use Illuminate\Http\Request;
use Validator;

class SubscriptionController extends Controller
{
    use ApiResponse;
    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        $subscriptions = Subscription::get();

        return $this->successResponse('Subscriptions retrieved successfully', SubscriptionResource::collection($subscriptions), 200);
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        $validator = Validator::make($request->all(),
        [
            'name' => 'required|string|max:255',
            'description' => 'required|string',
            'price' => 'required|numeric|min:0',
            'lesson_type_id' => 'required|integer|exists:lesson_types,id',
            'duration' => 'required|integer|min:1'
        ]);

        if($validator->fails()) {
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
            return $this->errorResponse('Subscription creation failed:'. $e->getMessage(), 500);
        }
    }

    /**
     * Display the specified resource.
     */
    public function show(string $id)
    {
        //
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, string $id)
    {
        $validator = Validator::make($request->all(),
        [
            'name' => 'sometimes|string|max:255',
            'description' => 'sometimes|string',
            'price' => 'sometimes|numeric|min:0',
            'lesson_type_id' => 'sometimes|integer|exists:lesson_types,id',
            'duration' => 'sometimes|integer|min:1'
        ]);

        if($validator->fails()) {
            return $this->errorResponse($validator->errors(), 422);
        }

        try {
            $subscription = Subscription::findOrFail($id);
            $subscription->update($request->only(['name', 'description', 'price', 'lesson_type_id', 'duration']));

            return $this->successResponse('Subscription updated successfully', SubscriptionResource::collection($subscription), 200);

        } catch (\Exception $e) {
            return $this->errorResponse('Subscription update failed: '. $e->getMessage(), 500);
        }
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(string $id)
    {
        try {
            $subscription = Subscription::findOrFail($id);
            $subscription->delete();

            return $this->successResponse('Subscription deleted successfully', SubscriptionResource::collection($subscription), 200);

        } catch (\Exception $e) {
            return $this->errorResponse('Subscription delete failed: '. $e->getMessage(), 500);
        }
    }
}
