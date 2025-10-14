<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Lesson;
use App\Models\Trainer;

class LessonTrainerController extends Controller
{
    /**
     * Assign a lesson to a trainer.
     */
    public function assign(Request $request)
    {
        $data = $request->validate([
            'lesson_id' => 'required|exists:lessons,id',
            'trainer_id' => 'required|exists:trainers,id',
        ]);

        $lesson = Lesson::findOrFail($data['lesson_id']);
        $trainer = Trainer::findOrFail($data['trainer_id']);

        // Assuming a many-to-many relationship between lessons and trainers
        $lesson->trainers()->attach($trainer);

        return response()->json(['message' => 'Lesson assigned to trainer successfully.'], 201);
    }

    /**
     * Remove a lesson assignment from a trainer.
     */
    public function unassign(Request $request, $lessonId, $trainerId)
    {
        $lesson = Lesson::findOrFail($lessonId);
        $trainer = Trainer::findOrFail($trainerId);

        $lesson->trainers()->detach($trainer);

        return response()->json(['message' => 'Lesson unassigned from trainer successfully.']);
    }
}