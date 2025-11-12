<?php

namespace App\Http\Controllers\Dashboard;

use App\Http\Controllers\Controller;
use App\Http\Helpers\ApiResponse;
use App\Http\Resources\Dashboard\LessonTypeResource;
use App\Models\LessonType;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Http\Request;
use Illuminate\Notifications\Notifiable;
use Illuminate\Support\Facades\Validator;
use Laravel\Sanctum\HasApiTokens;

/**
 * @OA\Tag(
 * name="Lesson Types",
 * description="API Endpoints for managing lesson types"
 * )
 */
class LessonTypeController extends Controller
{
    use ApiResponse, HasApiTokens, HasFactory, Notifiable;

    /**
     * @OA\Get(
     * path="/api/v1/lesson-types",
     * summary="Get a list of lesson types",
     * description="Returns a paginated list of all lesson types.",
     * tags={"Lesson Types"},
     * security={{"bearerAuth":{}}},
     * @OA\Response(
     * response=200,
     * description="Successful operation",
     * @OA\JsonContent(
     * type="object",
     * @OA\Property(property="message", type="string", example="Lesson type retrieved successfully"),
     * @OA\Property(property="data", type="object",
     * @OA\Property(property="items", type="array", @OA\Items(ref="#/components/schemas/LessonTypeResource")),
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
        $lessonType = LessonType::orderBy('created_at', 'desc')
            ->paginate(config('pagination.perPage'));

        return $this->successResponse('Lesson type retrieved successfully', $this->buildPaginatedResourceResponse(LessonTypeResource::class, $lessonType), 200);
    }

    /**
     * @OA\Post(
     * path="/api/v1/lesson-types",
     * summary="Create a new lesson type",
     * description="Creates a new lesson type.",
     * tags={"Lesson Types"},
     * security={{"bearerAuth":{}}},
     * @OA\RequestBody(
     * required=true,
     * @OA\JsonContent(
     * required={"name", "description"},
     * @OA\Property(property="name", type="string", example="Yoga"),
     * @OA\Property(property="description", type="text", example="    Lorem ipsum dolor sit amet consectetur adipisicing elit. Laudantium reprehenderit incidunt nemo ipsam qui. Architecto, autem officiis ipsum dolores illo neque dolorum accusantium fugit ab, nemo cum magni illum veniam!"),
     * )
     * ),
     * @OA\Response(
     * response=201,
     * description="Lesson type created successfully",
     * @OA\JsonContent(ref="#/components/schemas/LessonTypeResource")
     * ),
     * @OA\Response(response=422, description="Validation error"),
     * @OA\Response(response=500, description="Lesson type creation failed"),
     * @OA\Response(response=401, description="Unauthenticated")
     * )
     */
    public function store(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'name' => 'required|string|max:255',
            'description' => 'required|string'
        ]);

        if ($validator->fails()) {
            return $this->errorResponse($validator->errors(), 422);
        }

        try {
            $lessonType = LessonType::create([
                'name' => $request->name,
                'description' => $request->description
            ]);

            return $this->successResponse('Lesson type created successfully.', new LessonTypeResource($lessonType), 201);
        } catch (\Exception $e) {
            return $this->errorResponse('Lesson type creation fail: ' . $e->getMessage(), 500);
        }
    }

    /**
     * @OA\Get(
     * path="/api/v1/lesson-types/{id}",
     * summary="Get a single lesson type",
     * description="Returns the details of a specific lesson type by their ID.",
     * tags={"Lesson Types"},
     * security={{"bearerAuth":{}}},
     * @OA\Parameter(
     * name="id",
     * in="path",
     * required=true,
     * description="ID of the lesson type",
     * @OA\Schema(type="string")
     * ),
     * @OA\Response(
     * response=200,
     * description="Lesson Type Fetched Successfully",
     * @OA\JsonContent(ref="#/components/schemas/LessonTypeResource")
     * ),
     * @OA\Response(response=404, description="Lesson type not found"),
     * @OA\Response(response=401, description="Unauthenticated")
     * )
     */
    public function show($id)
    {
        $lessonType = LessonType::find($id);

        if (!$lessonType) {
            return $this->errorResponse('Lesson type not found.', 404);
        }

        return $this->successResponse('Lesson type fetched successfully', new LessonTypeResource($lessonType), 200);
    }

    /**
     * @OA\Put(
     * path="/api/v1/lesson-types/{id}",
     * summary="Update an existing lesson type",
     * description="Updates the details of an existing lesson type.",
     * tags={"Lesson Types"},
     * security={{"bearerAuth":{}}},
     * @OA\Parameter(
     * name="id",
     * in="path",
     * required=true,
     * description="ID of the lesson type to update",
     * @OA\Schema(type="string")
     * ),
     * @OA\RequestBody(
     * required=true,
     * @OA\JsonContent(
     * required={"name", "description"},
     * @OA\Property(property="name", type="string", example="Yoga"),
     * @OA\Property(property="description", type="text", example="    Lorem ipsum dolor sit amet consectetur adipisicing elit. Laudantium reprehenderit incidunt nemo ipsam qui. Architecto, autem officiis ipsum dolores illo neque dolorum accusantium fugit ab, nemo cum magni illum veniam!"),
     * )
     * ),
     * @OA\Response(
     * response=200,
     * description="Lesson Type Updated Successfully",
     * @OA\JsonContent(ref="#/components/schemas/LessonTypeResource")
     * ),
     * @OA\Response(response=422, description="Validation error"),
     * @OA\Response(response=404, description="Lesson type not found"),
     * @OA\Response(response=401, description="Unauthenticated")
     * )
     */
    public function update(Request $request, $id)
    {
        $lessonType = LessonType::find($id);

        if (!$lessonType) {
            return $this->errorResponse('Lesson type not found.', 404);
        }

        $validator = Validator::make($request->all(), [
            'name' => 'required|string|max:255',
            'description' => 'required|string'
        ]);

        if ($validator->fails()) {
            return $this->errorResponse($validator->errors(), 422);
        }

        try {
            $lessonType->update([
                'name' => $request->name,
                'description' => $request->description
            ]);

            return $this->successResponse('Lesson type updated.', new LessonTypeResource($lessonType), 200);
        } catch (\Exception $e) {
            return $this->errorResponse('Lesson type creation fail: ' . $e->getMessage(), 500);
        }
    }

    /**
     * @OA\Delete(
     *     path="/api/lesson-types/{id}",
     *     summary="Delete a specific lesson type",
     *     description="This endpoint permanently deletes a lesson type record from the system using its unique ID.",
     *     tags={"Lesson Types"},
     *
     *     @OA\Parameter(
     *         name="id",
     *         in="path",
     *         required=true,
     *         description="ID of the lesson type to delete",
     *         @OA\Schema(type="integer", example=5)
     *     ),
     *
     *     @OA\Response(
     *         response=204,
     *         description="Lesson type deleted successfully",
     *         @OA\JsonContent(
     *             @OA\Property(property="message", type="string", example="Lesson type deleted successfully.")
     *         )
     *     ),
     *
     *     @OA\Response(
     *         response=404,
     *         description="Lesson type not found",
     *         @OA\JsonContent(
     *             @OA\Property(property="message", type="string", example="Lesson type not found.")
     *         )
     *     ),
     *
     *     @OA\Response(
     *         response=500,
     *         description="Internal server error",
     *         @OA\JsonContent(
     *             @OA\Property(property="message", type="string", example="Something went wrong while deleting lesson type.")
     *         )
     *     )
     * )
     */
    public function destory($id)
    {
        $lessonType = LessonType::find($id);

        if (!$lessonType) {
            return $this->errorResponse('Lesson type not found.', 404);
        }

        $lessonType->delete();

        return $this->successResponse('Lesson type deleted.', new LessonTypeResource($lessonType), 204);
    }
}
