<?php

namespace App\Http\Controllers\Dashboard;

use App\Http\Controllers\Controller;
use App\Http\Helpers\ApiResponse;
use App\Http\Resources\Dashboard\TestimonialResource;
use App\Models\Testimonial;

/**
 * @OA\Tag(
 * name="Testimonials",
 * description="API Endpoints for managing testimonials"
 * )
 */
class TestimonialController extends Controller
{
    use ApiResponse;

    /**
     * @OA\Get(
     * path="/api/v1/testimonials",
     * summary="Get a list of testimonials",
     * description="Returns a paginated list of all testimonials.",
     * tags={"Testimonials"},
     * security={{"bearerAuth":{}}},
     * @OA\Response(
     * response=200,
     * description="Successful operation",
     * @OA\JsonContent(
     * type="object",
     * @OA\Property(property="message", type="string", example="Testimonials retrieved successfully"),
     * @OA\Property(property="data", type="object",
     * @OA\Property(property="items", type="array", @OA\Items(ref="#/components/schemas/TestimonialResource")),
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
        $testimonials = Testimonial::with('user:id,full_name,email')->latest()->get();

        return $this->successResponse('Testimonial retrieved successfully.', TestimonialResource::collection($testimonials), 200);
    }

    /**
     * @OA\Delete(
     *     path="/api/testimonials/{id}",
     *     summary="Delete a specific testimonial",
     *     description="This endpoint permanently deletes a testimonial record from the system using its unique ID.",
     *     tags={"Testimonials"},
     *     security={{"bearerAuth":{}}},
     *
     *     @OA\Parameter(
     *         name="id",
     *         in="path",
     *         required=true,
     *         description="ID of the testimonial to delete",
     *         @OA\Schema(type="integer", example=5)
     *     ),
     *
     *     @OA\Response(
     *         response=204,
     *         description="Testimonial deleted successfully",
     *         @OA\JsonContent(
     *             @OA\Property(property="message", type="string", example="Testimonial deleted successfully.")
     *         )
     *     ),
     *
     *     @OA\Response(
     *         response=404,
     *         description="Testimonial not found",
     *         @OA\JsonContent(
     *             @OA\Property(property="message", type="string", example="Testimonial not found.")
     *         )
     *     ),
     *
     *     @OA\Response(
     *         response=500,
     *         description="Internal server error",
     *         @OA\JsonContent(
     *             @OA\Property(property="message", type="string", example="Something went wrong while deleting testimonial.")
     *         )
     *     )
     * )
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
