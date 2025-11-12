<?php

namespace App\Http\Controllers\Client;

use App\Http\Controllers\Controller;
use App\Http\Helpers\ApiResponse;
use App\Http\Resources\Client\TestimonialResource;
use App\Models\Testimonial;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;

class TestimonialController extends Controller
{
    use ApiResponse;

    /**
     * @OA\Post(
     * path="/api/v1/testimonials/{id}/create",
     * summary="Create a new user's testimonial",
     * description="Create a new user's testimonial for a specific user ID",
     * tags={"Client Testimonials"},
     * security={{"bearerAuth":{}}},
     * @OA\Parameter(
     * name="id",
     * in="path",
     * required=true,
     * description="The ID of the user the testimonial is being created FOR (or BY, depending on your logic)",
     * @OA\Schema(type="integer", format="int64", example=1)
     * ),
     * @OA\RequestBody(
     * required=true,
     * description="Testimonial details",
     * @OA\JsonContent(
     * required={"comment"},
     * @OA\Property(property="comment", type="string", description="The content of the testimonial.", example="Minima id quidem eius reiciendis hic aut expedita velit quod et aut molestias."),
     * )
     * ),
     * @OA\Response(
     * response=201,
     * description="Testimonial created successfully",
     * @OA\JsonContent(
     * @OA\Property(property="success", type="boolean", example=true),
     * @OA\Property(property="message", type="string", example="Testimonial created successfully"),
     * @OA\Property(property="content", ref="#/components/schemas/TestimonialResource"),
     * @OA\Property(property="status", type="integer", example=201)
     * )
     * ),
     * @OA\Response(
     * response=422,
     * description="Validation error",
     * @OA\JsonContent(
     * @OA\Property(property="success", type="boolean", example=false),
     * @OA\Property(
     * property="message",
     * type="object",
     * description="Validation error messages, keyed by field name.",
     * @OA\Property(
     * property="comment",
     * type="array",
     * @OA\Items(type="string", example="The comment field is required.")
     * )
     * )
     * )
     * ),
     * @OA\Response(
     * response=401,
     * description="Unauthenticated",
     * @OA\JsonContent(
     * @OA\Property(property="message", type="string", example="Unauthenticated")
     * )
     * )
     * )
     */
    public function store(Request $request, $id)
    {
        $validator = Validator::make($request->all(), [
            'comment' => 'required|string|max:500',
        ]);

        if ($validator->fails()) {
            return $this->errorResponse($validator->errors(), 422);
        }

        $testimonial = Testimonial::create([
            'user_id' => $id,
            'comment' => $request->comment,
        ]);

        return $this->successResponse('Testimonial created successfully', new TestimonialResource($testimonial), 201);
    }

    /**
     * @OA\Delete(
     *     path="/api/testimonials/{id}/{testimonialId}/delete",
     *     summary="Delete a specific testimonial",
     *     description="This endpoint permanently deletes a testimonial record from the system using its unique ID.",
     *     tags={"Client Testimonials"},
     *     security={{"bearerAuth":{}}},
     *
     *     @OA\Parameter(
     *         name="id",
     *         in="path",
     *         required=true,
     *         description="ID of the user to delete",
     *         @OA\Schema(type="integer", example=1)
     *     ),
     *
     *     @OA\Parameter(
     *         name="testimonialId",
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
    public function destroy($id, $testimonialId)
    {
        $testimonial = Testimonial::find($testimonialId)->where('user_id', $id);

        if (!$testimonial) {
            return $this->errorResponse('Testimonial not found.', 404);
        }

        $testimonial->delete();

        return $this->successResponse('Testimonial deleted successfully.', null, 204);
    }
}
