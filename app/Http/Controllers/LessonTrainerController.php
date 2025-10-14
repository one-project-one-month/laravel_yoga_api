<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use App\Models\User;
use App\Models\LessonType;

class LessonTrainerController extends Controller
{
    /**
     * List assignments (simple).
     */
    public function index(Request $request)
    {
        $rows = DB::table('lesson_trainer')
            ->join('users', 'lesson_trainer.trainer_id', '=', 'users.id')
            ->join('lesson_types', 'lesson_trainer.lesson_type_id', '=', 'lesson_types.id')
            ->select(
                'lesson_trainer.id',
                'lesson_trainer.trainer_id',
                'users.name as trainer_name',
                'lesson_trainer.lesson_type_id',
                'lesson_types.name as lesson_type_name',
                'lesson_trainer.created_at'
            )
            ->orderBy('lesson_trainer.created_at', 'desc')
            ->paginate(15);

        return response()->json($rows);
    }

    /**
     * Attach a trainer to a lesson type.
     * Expects JSON: { "trainer_id": 1, "lesson_type_id": 2 }
     */
    public function store(Request $request)
    {
        $data = $request->validate([
            'trainer_id' => 'required|integer|exists:users,id',
            'lesson_type_id' => 'required|integer|exists:lesson_types,id',
        ]);

        $exists = DB::table('lesson_trainer')
            ->where('trainer_id', $data['trainer_id'])
            ->where('lesson_type_id', $data['lesson_type_id'])
            ->exists();

        if ($exists) {
            return response()->json([
                'message' => 'Trainer already assigned to this lesson type.'
            ], 200);
        }

        DB::table('lesson_trainer')->insert([
            'trainer_id' => $data['trainer_id'],
            'lesson_type_id' => $data['lesson_type_id'],
            'created_at' => now(),
            'updated_at' => now(),
        ]);

        return response()->json([
            'message' => 'Trainer assigned to lesson type.',
            'trainer_id' => $data['trainer_id'],
            'lesson_type_id' => $data['lesson_type_id'],
        ], 201);
    }

    /**
     * Remove an assignment.
     * Accepts query or body params: trainer_id + lesson_type_id or assignment id.
     */
    public function destroy(Request $request, $id = null)
    {
        // if id provided, delete by pivot id
        if ($id) {
            $deleted = DB::table('lesson_trainer')->where('id', $id)->delete();

            return response()->json([
                'deleted' => (bool) $deleted,
            ], $deleted ? 200 : 404);
        }

        // otherwise expect trainer_id + lesson_type_id in body
        $data = $request->validate([
            'trainer_id' => 'required_without:id|integer|exists:users,id',
            'lesson_type_id' => 'required_without:id|integer|exists:lesson_types,id',
        ]);

        $deleted = DB::table('lesson_trainer')
            ->where('trainer_id', $data['trainer_id'])
            ->where('lesson_type_id', $data['lesson_type_id'])
            ->delete();

        return response()->json([
            'deleted' => (bool) $deleted,
        ], $deleted ? 200 : 404);
    }
}
