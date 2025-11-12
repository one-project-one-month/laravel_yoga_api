<?php

namespace App\Http\Controllers\Dashboard;

use App\Http\Controllers\Controller;
use App\Http\Helpers\ApiResponse;
use App\Http\Resources\Dashboard\LessonResource;
use App\Models\Lesson;
use App\Models\SubscriptionUser;
use CloudinaryLabs\CloudinaryLaravel\Facades\Cloudinary;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Http\Request;
use Illuminate\Notifications\Notifiable;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Validator;
use Laravel\Sanctum\HasApiTokens;

/**
 * @OA\Tag(
 * name="Lessons",
 * description="API Endpoints for managing lessons"
 * )
 */
class LessonController extends Controller
{
    use ApiResponse, HasApiTokens, HasFactory, Notifiable;

    /**
     * @OA\Get(
     * path="/api/v1/lessons",
     * summary="Get a list of lessons",
     * description="Returns a paginated list of all lessons.",
     * tags={"Lessons"},
     * security={{"bearerAuth":{}}},
     * @OA\Response(
     * response=200,
     * description="Successful operation",
     * @OA\JsonContent(
     * type="object",
     * @OA\Property(property="message", type="string", example="Lessons retrieved successfully"),
     * @OA\Property(property="data", type="object",
     * @OA\Property(property="items", type="array", @OA\Items(ref="#/components/schemas/LessonResource")),
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
        $user = Auth::user();

        if ($user->role_id == 1 || $user->role_id == 2) {
            $lesson = Lesson::orderBy('created_at', 'desc')
                ->paginate(config('pagination.perPage'));

            return $this->successResponse('Lesson retrieved successfully', $this->buildPaginatedResourceResponse(LessonResource::class, $lesson), 200);
        }

        if ($user->role_id == 3) {
            $activeLesson = $user
                ->subscriptions()
                ->wherePivot('status', 'active')
                ->wherePivot('end_date', '>=', now())
                ->pluck('lesson_type_id');

            if ($activeLesson->isNotEmpty()) {
                $lesson = Lesson::where('is_free', true)
                    ->orWhereIn('lesson_type_id', $activeLesson)
                    ->paginate(config('pagination.perPage'));
            } else {
                $lesson = Lesson::where('is_free', true)->paginate(config('pagination.perPage'));
            }

            return $this->successResponse('Lesson retrieved successfully', $this->buildPaginatedResourceResponse(LessonResource::class, $lesson), 200);
        }
    }

    /**
     * @OA\Post(
     * path="/api/v1/lessons",
     * summary="Create a new lesson",
     * description="Creates a new lesson.",
     * tags={"Lessons"},
     * security={{"bearerAuth":{}}},
     * @OA\RequestBody(
     * required=true,
     * @OA\JsonContent(
     * required={"title", "slug", "description", "level", "video", "lessonTypeId", "durationMinutes"},
     * @OA\Property(property="title", type="string", example="Reprehenderit accusamus."),
     * @OA\Property(property="slug", type="string", example="blanditiis-aliquam-et-debitis-ex-totam-praesentium-exercitationem"),
     * @OA\Property(property="description", type="string", example="Velit autem fuga distinctio autem atque quaerat. Maiores eius recusandae maxime illum quibusdam animi inventore. Quia voluptate voluptates doloremque aperiam labore quos numquam. Rerum autem velit tempore illo quia ut. Sit doloribus voluptates nisi tempore."),
     * @OA\Property(property="level", type="string", example="beginner"),
     * @OA\Property(property="video", type="string", format="file", example="examplevideo.mp4"),
     * @OA\Property(property="lessonTypeId", type="integer", example="2"),
     * @OA\Property(property="durationMinutes", type="integer", example="45"),
     * )
     * ),
     * @OA\Response(
     * response=201,
     * description="Lesson created successfully",
     * @OA\JsonContent(ref="#/components/schemas/LessonResource")
     * ),
     * @OA\Response(response=422, description="Validation error"),
     * @OA\Response(response=500, description="Lesson creation failed"),
     * @OA\Response(response=401, description="Unauthenticated")
     * )
     */
    public function store(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'title' => 'required',
            'slug' => 'required|unique:lessons,slug',
            'description' => 'required',
            'level' => 'required',
            'video' => 'required|file|mimes:mp4,avi,mov',
            'lessonTypeId' => 'required',
            'durationMinutes' => 'required',
            'trainerId' => 'required'
        ]);

        if ($validator->fails()) {
            return $this->errorResponse($validator->errors(), 422);
        }

        if ($request->hasFile('video')) {
            $uploaded = Cloudinary::uploadVideo($request->file('video')->getRealPath(), ['folder' => 'lessons', 'resources_type' => 'video']);
            $videoUrl = $uploaded->getSecurePath();
            $videoPublicId = $uploaded->getPublicId();
        }

        try {
            $lesson = Lesson::create([
                'title' => $request->title,
                'slug' => $request->slug,
                'description' => $request->description,
                'level' => $request->level,
                'video_url' => $videoUrl,
                'video_public_id' => $videoPublicId,
                'lesson_type_id' => $request->lessonTypeId,
                'duration_minutes' => $request->durationMinutes,
                'trainer_id' => $request->trainerId
            ]);

            return $this->successResponse('Lesson created successfully.', new LessonResource($lesson), 201);
        } catch (\Exception $e) {
            return $this->errorResponse('Lesson creation failed:' . $e->getMessage(), 500);
        }
    }

    /**
     * @OA\Get(
     * path="/api/v1/lessons/{id}",
     * summary="Get a single lesson",
     * description="Returns the details of a specific lesson by their ID.",
     * tags={"Lessons"},
     * security={{"bearerAuth":{}}},
     * @OA\Parameter(
     * name="id",
     * in="path",
     * required=true,
     * description="ID of the lesson",
     * @OA\Schema(type="string")
     * ),
     * @OA\Response(
     * response=200,
     * description="Lesson Fetched Successfully",
     * @OA\JsonContent(ref="#/components/schemas/LessonResource")
     * ),
     * @OA\Response(response=404, description="Lesson not found"),
     * @OA\Response(response=401, description="Unauthenticated")
     * )
     */
    public function show($id)
    {
        $lesson = Lesson::find($id);

        if (!$lesson) {
            return $this->errorResponse('Lesson not found', 404);
        }

        return $this->successResponse('Lesson fetched successfully.', new LessonResource($lesson), 200);
    }

    /**
     * @OA\Put(
     * path="/api/v1/lessons/{id}",
     * summary="Update an existing lesson",
     * description="Updates the details of an existing lesson.",
     * tags={"Lessons"},
     * security={{"bearerAuth":{}}},
     * @OA\Parameter(
     * name="id",
     * in="path",
     * required=true,
     * description="ID of the lesson to update",
     * @OA\Schema(type="string")
     * ),
     * @OA\RequestBody(
     * required=true,
     * @OA\JsonContent(
     * required={"title", "slug", "description", "level", "video", "lessonTypeId", "durationMinutes"},
     * @OA\Property(property="title", type="string", example="Reprehenderit accusamus."),
     * @OA\Property(property="slug", type="string", example="blanditiis-aliquam-et-debitis-ex-totam-praesentium-exercitationem"),
     * @OA\Property(property="description", type="string", example="Velit autem fuga distinctio autem atque quaerat. Maiores eius recusandae maxime illum quibusdam animi inventore. Quia voluptate voluptates doloremque aperiam labore quos numquam. Rerum autem velit tempore illo quia ut. Sit doloribus voluptates nisi tempore."),
     * @OA\Property(property="level", type="string", example="beginner"),
     * @OA\Property(property="video", type="string", format="file", example="examplevideo.mp4"),
     * @OA\Property(property="lessonTypeId", type="integer", example="2"),
     * @OA\Property(property="durationMinutes", type="integer", example="45"),
     * )
     * ),
     * @OA\Response(
     * response=200,
     * description="Lesson Updated Successfully",
     * @OA\JsonContent(ref="#/components/schemas/LessonResource")
     * ),
     * @OA\Response(response=422, description="Validation error"),
     * @OA\Response(response=404, description="Lesson not found"),
     * @OA\Response(response=401, description="Unauthenticated")
     * )
     */
    public function update(Request $request, $id)
    {
        $lesson = Lesson::find($id);

        if (!$lesson) {
            return $this->errorResponse('Lesson not found', 404);
        }

        $validator = Validator::make($request->all(), [
            'title' => 'required',
            'slug' => 'required|unique:lessons,slug,' . $id,
            'description' => 'required',
            'level' => 'required',
            'video' => 'required|file|mimes:mp4,avi,mov',
            'lessonTypeId' => 'required',
            'durationMinutes' => 'required',
            'trainerId' => 'required'
        ]);

        if ($validator->fails()) {
            return $this->errorResponse($validator->errors(), 422);
        }

        $lessonData = [
            'title' => $request->title,
            'slug' => $request->slug,
            'description' => $request->description,
            'level' => $request->level,
            'duration_minutes' => $request->durationMinutes,
            'trainer_id' => $request->trainerId
        ];

        if ($request->hasFile($request->video)) {
            if ($lesson->video_url) {
                Cloudinary::destroy($lesson->public_id, ['resources_types' => 'video']);
            }
            $uploaded = Cloudinary::uploadVideo($request->file('video')->getRealPath(), ['folder' => 'lessons', 'resources_type' => 'video']);
            $lessonData['video_url'] = $uploaded->getSecurePath();
            $lessonData['video_public_id'] = $uploaded->getPublicId();
        }

        try {
            $lesson->update($lessonData);

            return $this->successResponse('Lesson updated successfully.', new LessonResource($lesson), 200);
        } catch (\Exception $e) {
            return $this->errorResponse('Lesson updated failed:' . $e->getMessage(), 500);
        }
    }

    /**
     * @OA\Delete(
     *     path="/api/lessons/{id}",
     *     summary="Delete a specific lesson",
     *     description="This endpoint permanently deletes a lesson record from the system using its unique ID.",
     *     tags={"Lessons"},
     *     security={{"bearerAuth":{}}},
     *
     *     @OA\Parameter(
     *         name="id",
     *         in="path",
     *         required=true,
     *         description="ID of the lesson to delete",
     *         @OA\Schema(type="integer", example=5)
     *     ),
     *
     *     @OA\Response(
     *         response=204,
     *         description="Lesson deleted successfully",
     *         @OA\JsonContent(
     *             @OA\Property(property="message", type="string", example="Lesson deleted successfully.")
     *         )
     *     ),
     *
     *     @OA\Response(
     *         response=404,
     *         description="Lesson not found",
     *         @OA\JsonContent(
     *             @OA\Property(property="message", type="string", example="Lesson not found.")
     *         )
     *     ),
     *
     *     @OA\Response(
     *         response=500,
     *         description="Internal server error",
     *         @OA\JsonContent(
     *             @OA\Property(property="message", type="string", example="Something went wrong while deleting lesson.")
     *         )
     *     )
     * )
     */
    public function destroy($id)
    {
        $lesson = Lesson::find($id);

        if (!$lesson) {
            return $this->errorResponse('Lesson not found', 404);
        }

        if ($lesson->video_public_id) {
            Cloudinary::destroy($lesson->video_public_id, ['resources_types' => 'video']);
        }

        $lesson->delete();

        $this->successResponse('Lesson deleted successfully', null, 204);
    }
}
