<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Lesson;
use Illuminate\Validation\Rule; // Import for unique validation in update

class LessonController extends Controller
{
    /**
     * GET /api/lessons
     * List lessons (paginated).
     */
    public function index(Request $request)
    {
        $perPage = (int) $request->query('per_page', 15);
        $query = Lesson::query()->orderBy('created_at', 'desc');

        if ($q = $request->query('q')) {
            // Searching across title and description
            $query->where(function ($q) use ($q) {
                $q->where('title', 'like', '%' . $q . '%')
                  ->orWhere('description', 'like', '%' . $q . '%');
            });
        }

        $lessons = $query->paginate($perPage);

        return response()->json($lessons, 200);
    }

    //--------------------------------------------------------------------------

    /**
     * GET /api/lessons/{id}
     * Show lesson details.
     */
    public function show(Request $request, $id)
    {
        $lesson = Lesson::findOrFail($id);
        return response()->json($lesson, 200);
    }

    //--------------------------------------------------------------------------

    /**
     * POST /api/lessons
     * Create new lesson.
     */
    public function store(Request $request)
    {
        // Validation now matches the lessons table schema
        $data = $request->validate([
            'title' => 'required|string|max:255',
            'slug' => 'required|string|max:255|unique:lessons,slug',
            'description' => 'required|string', // Corrected: was 'content'
            'level' => 'required|in:beginner,intermediate,advanced',
            'video_url' => 'required|url|max:255',
            'video_public_id' => 'nullable|string|max:255',
            'lesson_type_id' => 'required|exists:lesson_types,id',
            'duration_minutes' => 'required|integer|min:0', // Required per table definition
            'is_free' => 'nullable|boolean',
            'is_premium' => 'nullable|boolean',
            'trainer_id' => 'required|exists:users,id',
            // NOTE: Removed the non-existent 'order' field.
        ]);

        $lesson = Lesson::create($data);

        return response()->json($lesson, 201);
    }

    //--------------------------------------------------------------------------

    /**
     * PUT /api/lessons/{id}
     * Update lesson.
     */
    public function update(Request $request, $id)
    {
        $lesson = Lesson::findOrFail($id);

        // Validation uses 'sometimes' for optional updates
        $data = $request->validate([
            'title' => 'sometimes|required|string|max:255',
            // Rule::unique allows excluding the current record ($id) from the check
            'slug' => [
                'sometimes', 
                'required', 
                'string', 
                'max:255', 
                Rule::unique('lessons', 'slug')->ignore($lesson->id)
            ],
            'description' => 'sometimes|required|string', // Corrected: was 'content'
            'level' => 'sometimes|required|in:beginner,intermediate,advanced',
            'video_url' => 'sometimes|required|url|max:255',
            'video_public_id' => 'nullable|string|max:255',
            'lesson_type_id' => 'sometimes|required|exists:lesson_types,id',
            'duration_minutes' => 'sometimes|required|integer|min:0',
            'is_free' => 'nullable|boolean',
            'is_premium' => 'nullable|boolean',
            'trainer_id' => 'sometimes|required|exists:users,id',
            // NOTE: Removed the non-existent 'order' field.
        ]);

        $lesson->update($data);

        return response()->json($lesson, 200);
    }

    //--------------------------------------------------------------------------

    /**
     * DELETE /api/lessons/{id}
     * Delete lesson.
     */
    public function destroy(Request $request, $id)
    {
        $lesson = Lesson::findOrFail($id);
        $lesson->delete();

        return response()->json(['message' => 'Lesson deleted.'], 200);
    }
}