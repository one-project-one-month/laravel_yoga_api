<?php

namespace App\Http\Controllers\Dashboard;

use App\Http\Controllers\Controller;
use App\Http\Helpers\ApiResponse;
use App\Http\Resources\Dashboard\LessonTrainerResource;
use App\Models\LessonTrainer;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;

class LessonTrainerController extends Controller
{
    use ApiResponse;

    /**
     * @OA\Post(
     *     path="/api/v1/lessons-trainers",
     *     summary="Assign a trainer to a lesson",
     *     description="This endpoint assigns a trainer to a specific lesson.",
     *     tags={"Lesson Trainer"},
     *     @OA\Parameter(
     *         name="lesson_type_id",
     *         in="path",
     *         required=true,
     *         description="Lesson Type ID",
     *         @OA\Schema(type="integer")
     *     ),
     *     @OA\Parameter(
     *         name="trainer_id",
     *         in="path",
     *         required=true,
     *         description="Trainer ID",
     *         @OA\Schema(type="integer")
     *     ),
     *     @OA\Response(
     *         response=200,
     *         description="Trainer assigned successfully",
     *         @OA\JsonContent(
     *             @OA\Property(property="message", type="string", example="Trainer assigned successfully")
     *         )
     *     ),
     * )
     */
    public function assign(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'lessonTypeId' => 'required|exists:lesson_types,id',
            'trainerId' => 'required|exists:trainer_details,id',
        ]);

        if ($validator->fails()) {
            return $this->errorResponse($validator->errors(), 422);
        }

        try {
            $lessonTrainer = LessonTrainer::create([
                'lesson_type_id' => $request->lessonTypeId,
                'trainer_id' => $request->trainerId
            ]);
            return $this->successResponse('Lesson trainer assign successfully.', new LessonTrainerResource($lessonTrainer), 201);
        } catch (\Exception $e) {
            return $this->errorResponse($e->getMessage(), 500);
        }
    }

    /**
     * @OA\Delete(
     *     path="/api/v1/lessons-trainers/{id}",
     *     summary="Unassign a trainer from a lesson",
     *     description="This endpoint removes the assigned trainer from a lesson.",
     *     tags={"Lesson Trainer"},
     *     @OA\Parameter(
     *         name="lesson_type_id",
     *         in="path",
     *         required=true,
     *         description="Lesson Type ID",
     *         @OA\Schema(type="integer")
     *     ),
     *     @OA\Parameter(
     *         name="trainer_id",
     *         in="path",
     *         required=true,
     *         description="Trainer ID",
     *         @OA\Schema(type="integer")
     *     ),
     *     @OA\Response(
     *         response=200,
     *         description="Trainer unassigned successfully",
     *         @OA\JsonContent(
     *             @OA\Property(property="message", type="string", example="Trainer unassigned successfully")
     *         )
     *     ),
     *     @OA\Response(
     *         response=404,
     *         description="Assignment not found"
     *     )
     * )
     */
    public function unassign($id)
    {
        $unassign = LessonTrainer::find($id, 'id');

        if (!$unassign) {
            return $this->errorResponse("Trainer's lesson not found.", 404);
        }

        $unassign->delete();

        return $this->successResponse("Trainer's lesson deleted.", null, 204);
    }
}
