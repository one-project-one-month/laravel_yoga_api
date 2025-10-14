<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Lesson;

class LessonController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index(Request $request)
    {
        $lessons = Lesson::orderBy('created_at', 'desc')->paginate(15);

        if ($request->wantsJson()) {
            return response()->json($lessons);
        }

        return view('dashboard_lesson', compact('lessons'));
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create(Request $request)
    {
        if ($request->wantsJson()) {
            return response()->json(['message' => 'Provide lesson data and POST to /lessons']);
        }

        return view('lessons.create');
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        $data = $request->validate([
            'title' => 'required|string|max:255',
            'content' => 'nullable|string',
            'duration_minutes' => 'nullable|integer|min:0',
            'order' => 'nullable|integer|min:0',
        ]);

        $lesson = Lesson::create($data);

        if ($request->wantsJson()) {
            return response()->json($lesson, 201);
        }

        return redirect()->route('lessons.show', $lesson->id)
            ->with('success', 'Lesson created.');
    }

    /**
     * Display the specified resource.
     */
    public function show(Request $request, $id)
    {
        $lesson = Lesson::findOrFail($id);

        if ($request->wantsJson()) {
            return response()->json($lesson);
        }

        return view('lessons.show', compact('lesson'));
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(Request $request, $id)
    {
        $lesson = Lesson::findOrFail($id);

        if ($request->wantsJson()) {
            return response()->json($lesson);
        }

        return view('lessons.edit', compact('lesson'));
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, $id)
    {
        $lesson = Lesson::findOrFail($id);

        $data = $request->validate([
            'title' => 'sometimes|required|string|max:255',
            'content' => 'nullable|string',
            'duration_minutes' => 'nullable|integer|min:0',
            'order' => 'nullable|integer|min:0',
        ]);

        $lesson->update($data);

        if ($request->wantsJson()) {
            return response()->json($lesson);
        }

        return redirect()->route('lessons.show', $lesson->id)
            ->with('success', 'Lesson updated.');
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(Request $request, $id)
    {
        $lesson = Lesson::findOrFail($id);
        $lesson->delete();

        if ($request->wantsJson()) {
            return response()->json(['message' => 'Lesson deleted.']);
        }

        return redirect()->route('lessons.index')
            ->with('success', 'Lesson deleted.');
    }
}