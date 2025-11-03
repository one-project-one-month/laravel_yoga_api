<?php

namespace App\Http\Controllers\Dashboard;

use App\Http\Controllers\Controller;
use App\Http\Helpers\ApiResponse;
use App\Http\Resources\Dashboard\TestimonialResource;
use App\Models\Testimonial;

class TestimonialController extends Controller
{
    use ApiResponse;

    /**
     * GET /api/v1/testimonials
     * List all testimonials
     */
    public function index()
    {
        $testimonials = Testimonial::with('user:id,full_name,email')->latest()->get();

        return $this->successResponse('Testimonial retrieved successfully.', TestimonialResource::collection($testimonials), 200);
    }

    /**
     * DELETE /api/v1/testimonials/{id}
     * Delete testimonial
     */
    public function destroy($id)
    {
        $testimonial = Testimonial::find($id);

        if (!$testimonial) {
            return $this->errorResponse('Testimonial not found.', 404);
        }

        $testimonial->delete();

        return $this->successResponse('Testimonial deleted successfully.', null, 204);
    }

}
