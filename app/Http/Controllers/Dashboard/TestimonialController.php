<?php

namespace App\Http\Controllers\Dashboard;

use App\Http\Controllers\Controller;
use App\Http\Helpers\ApiResponse;
use App\Http\Resources\Dashboard\TestimonialResource;
use Illuminate\Http\Request;
use App\Models\Testimonial;
use Illuminate\Support\Facades\Validator;

class TestimonialController extends Controller
{
    use ApiResponse;
    public function index()
    {
        $testimonials = Testimonial::with('user:id,full_name,email')->latest()->get();

        return $this->successResponse('Testimonial retrieved successfully.', TestimonialResource::collection($testimonials), 200);
    }
    public function store(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'comment' => 'required|string|max:500',
        ]);

        if ($validator->fails()) {
            return $this->errorResponse($validator->errors(), 422);
        }

        // Create testimonial for the authenticated user
        $testimonial = Testimonial::create([
            'user_id' => $request->user()->id,
            'comment' => $request->comment,
        ]);

        return $this->successResponse('Testimonial created successfully', new TestimonialResource($testimonial), 201);
    }
}
