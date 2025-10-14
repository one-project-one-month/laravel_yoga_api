<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\LessonType;

class LessonTypeController extends Controller
{
    /**
     * Display a listing of the lesson types.
     */
    public function index(Request $request)
    {
        $lessonTypes = LessonType::orderBy('created_at', 'desc')->paginate(15);

        if ($request->wantsJson()) {
            return response()->json($lessonTypes);
        }

        return view('lesson_types.index', compact('lessonTypes'));
    }

    /**
     * Store a newly created lesson type in storage.
     */
    public function store(Request $request)
    {
        $data = $request->validate([
            'name' => 'required|string|max:255',
        ]);

        $lessonType = LessonType::create($data);

        if ($request->wantsJson()) {
            return response()->json($lessonType, 201);
        }

        return redirect()->route('lesson-types.show', $lessonType->id)
            ->with('success', 'Lesson type created.');
    }

    /**
     * Display the specified lesson type.
     */
    public function show(Request $request, $id)
    {
        $lessonType = LessonType::findOrFail($id);

        if ($request->wantsJson()) {
            return response()->json($lessonType);
        }

        return view('lesson_types.show', compact('lessonType'));
    }

    /**
     * Update the specified lesson type in storage.
     */
    public function update(Request $request, $id)
    {
        $lessonType = LessonType::findOrFail($id);

        $data = $request->validate([
            'name' => 'sometimes|required|string|max:255',
        ]);

        $lessonType->update($data);

        if ($request->wantsJson()) {
            return response()->json($lessonType);
        }

        return redirect()->route('lesson-types.show', $lessonType->id)
            ->with('success', 'Lesson type updated.');
    }

    /**
     * Remove the specified lesson type from storage.
     */
    public function destroy(Request $request, $id)
    {
        $lessonType = LessonType::findOrFail($id);
        $lessonType->delete();

        if ($request->wantsJson()) {
            return response()->json(['message' => 'Lesson type deleted.']);
        }

        return redirect()->route('lesson-types.index')
            ->with('success', 'Lesson type deleted.');
    }
}