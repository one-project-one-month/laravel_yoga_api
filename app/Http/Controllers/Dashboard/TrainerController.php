<?php

namespace App\Http\Controllers\Dashboard;

use App\Http\Controllers\Controller;
use App\Http\Helpers\ApiResponse;
use App\Http\Resources\Dashboard\TrainerResource;
use Illuminate\Http\Request;
use App\Models\TrainerDetail;
use App\Models\User;
use Illuminate\Support\Facades\Validator;

class TrainerController extends Controller
{
    use ApiResponse;
    public function index()
    {
        $trainers = TrainerDetail::with('trainer:id,full_name,email')->get();

        return $this->successResponse('Trainer retrieved successfully.', TrainerResource::collection($trainers), 200);
    }


    public function store(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'trainer_id' => 'required|exists:users,id',
            'bio' => 'required|string',
            'universityName' => 'required|string',
            'degree' => 'required',
            'city' => 'required',
            'startDate' => 'required',
            'endDate' => 'required',
            'salary' => 'nullable|numeric|min:0',
            'branch_location' => 'nullable|string|max:255',
        ]);

        if ($validator->fails()) {
            return $this->errorResponse($validator->errors(), 422);
        }

        $trainerDetail = TrainerDetail::create($request->all());

        return $this->successResponse('Trainer created successfully.', new TrainerResource($trainerDetail), 201);
    }


    public function update(Request $request, $id)
    {
        $trainer = TrainerDetail::find($id);

        if (!$trainer) {
            return $this->errorResponse('Trainer not found.', 404);
        }

        $validator = Validator::make($request->all(), [
            'trainer_id' => 'required|exists:users,id',
            'bio' => 'required|string',
            'universityName' => 'required|string',
            'degree' => 'required',
            'city' => 'required',
            'startDate' => 'required',
            'endDate' => 'required',
            'salary' => 'nullable|numeric|min:0',
            'branch_location' => 'nullable|string|max:255',
        ]);

        if ($validator->fails()) {
            return $this->errorResponse($validator->errors(), 422);
        }

        $trainer->update($request->all());

        return $this->successResponse('Trainer updated successfully.', new TrainerResource($trainer), 200);
    }
}
