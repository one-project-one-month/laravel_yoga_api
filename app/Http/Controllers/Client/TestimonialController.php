<?php

namespace App\Http\Controllers\Client;

use App\Models\Testimonial;
use Illuminate\Http\Request;
use App\Http\Helpers\ApiResponse;
use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\Validator;
use App\Http\Resources\Client\TestimonialResource;

class TestimonialController extends Controller
{
    use ApiResponse;

    /**
     * POST /api/v1/testimonials
     * Create testimonial
     */
    public function store(Request $request){
        $validator = Validator::make($request->all(), [
            'comment' => 'required|string|max:500',
        ]);

        if ($validator->fails()) {
            return $this->errorResponse($validator->errors(), 422);
        }

        $testimonial = Testimonial::create([
            'user_id' => $request->user()->id,
            'comment' => $request->comment,
        ]);

        return $this->successResponse('Testimonial created successfully', new TestimonialResource($testimonial), 201);
    }

    /**
     * DELETE /api/v1/testimonials/{userId}/{id}
     * Delete their own testimonial
     */
    public function destroy($userId, $id)
    {
        $testimonial = Testimonial::find($id)->where('user_id', $userId);

        if (!$testimonial) {
            return $this->errorResponse('Testimonial not found.', 404);
        }

        $testimonial->delete();

        return $this->successResponse('Testimonial deleted successfully.', null, 204);
    }
}
