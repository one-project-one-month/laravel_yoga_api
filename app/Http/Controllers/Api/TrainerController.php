<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\TrainerDetail;
use App\Models\User;

class TrainerController extends Controller
{   
    public function index()
    {
        $trainers = TrainerDetail::with('trainer:id,name,email')->get();

        return response()->json([
            'success' => true,
            'data' => $trainers
        ]);
    }

  
    public function store(Request $request)
    {
        $request->validate([
            'trainer_id' => 'required|exists:users,id',
            'bio' => 'required|string',
            'description' => 'required|string',
            'salary' => 'required|numeric|min:0',
            'branch_location' => 'required|string|max:255',
        ]);

        $trainerDetail = TrainerDetail::create($request->all());

        return response()->json([
            'success' => true,
            'message' => 'Trainer profile created successfully',
            'data' => $trainerDetail
        ], 201);
    }

   
    public function update(Request $request, $id)
    {
        $trainer = TrainerDetail::find($id);

        if (! $trainer) {
            return response()->json([
                'success' => false,
                'message' => 'Trainer not found'
            ], 404);
        }

        $request->validate([
            'bio' => 'sometimes|string',
            'description' => 'sometimes|string',
            'salary' => 'sometimes|numeric|min:0',
            'branch_location' => 'sometimes|string|max:255',
        ]);

        $trainer->update($request->all());

        return response()->json([
            'success' => true,
            'message' => 'Trainer profile updated successfully',
            'data' => $trainer
        ]);
    }
}
