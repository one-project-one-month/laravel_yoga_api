<?php

namespace App\Http\Controllers\Dashboard;

use App\Models\Lesson;
use App\Models\SubscriptionUser;
use Illuminate\Http\Request;
use App\Http\Helpers\ApiResponse;
use Laravel\Sanctum\HasApiTokens;
use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\Auth;
use Illuminate\Notifications\Notifiable;
use Illuminate\Support\Facades\Validator;
use App\Http\Resources\Dashboard\LessonResource;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use CloudinaryLabs\CloudinaryLaravel\Facades\Cloudinary;

class LessonController extends Controller
{
    use ApiResponse, HasApiTokens, HasFactory, Notifiable;

    /**
     * GET /api/v1/lessons
     * List all lessons
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
            $activeLesson = $user->subscriptions()
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
     * POST /api/v1/lessons
     * Create new lesson
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
     * GET /api/v1/lessons/{id}
     * Show lesson information
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
     * PUT /api/v1/lessons/{id}
     * Update lesson information
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
     * DELETE /api/v1/lessons/{id}
     * Delete lesson video
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
