<?php

namespace App\Http\Controllers\Dashboard;

use App\Http\Resources\Dashboard\LessonTypeResource;
use App\Models\LessonType;
use App\Http\Helpers\ApiResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;
use Laravel\Sanctum\HasApiTokens;
use App\Http\Controllers\Controller;
use Illuminate\Notifications\Notifiable;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class LessonTypeController extends Controller
{
    use ApiResponse, HasApiTokens, HasFactory, Notifiable;

    /**
     * GET /api/v1/lesson-types
     * List all lesson type
    */
    public function index()
    {
        $lessonType = LessonType::orderBy('created_at', 'desc')
            ->paginate(config('pagination.perPage'));

        return $this->successResponse('Lesson type retrieved successfully', $this->buildPaginatedResourceResponse(LessonTypeResource::class, $lessonType), 200);
    }

    /**
     * POST /api/v1/lesson-type
     * Create new lesson type
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
     * GET /api/v1/lesson-type/{id}
     * Show lesson-type information
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
     * PUT /api/v1/lesson-type/{id}
     * Show lesson-type information
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
     * DELETE /api/v1/lesson-type/{id}
     * Delete lesson type
    */
    public function destory($id) {
        $lessonType = LessonType::find($id);

        if (!$lessonType) {
            return $this->errorResponse('Lesson type not found.', 404);
        }

        $lessonType->delete();

        return $this->successResponse('Lesson type deleted.', new LessonTypeResource($lessonType), 204);
    }
}
