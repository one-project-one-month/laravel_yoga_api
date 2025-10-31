<?php

namespace App\Http\Controllers\Client;

use App\Models\User;
use App\Models\Appointment;
use Illuminate\Http\Request;
use App\Http\Helpers\ApiResponse;
use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\Validator;
use App\Http\Resources\Client\AppointmentResource;

class AppointmentController extends Controller
{
    use ApiResponse;

    public function create(Request $request, $id) {
        $validator = Validator::make($request->all(), [
            'name' => 'required',
            'email' => "required|email",
            'appointmentDate' => 'required|date',
            'appointmentTime' => 'required|date_format:H:i',
            'appointmentType' => 'required'
        ]);

        if ($validator->fails()) {
            return $this->errorResponse($validator->errors(), 422);
        }

        $user = User::find($id, 'id');

        // logger($user);

        if($user->is_first_time_appointment == true) {
            $appointment = Appointment::create([
                'user_id' => $id,
                'appointment_date' => $request->appointmentDate,
                'appointment_time' => $request->appointmentTime,
                'appointment_type' => $request->appointmentType,
                "is_approved" => "pending",
                "appointment_fees" => 0
            ]);

            return $this->successResponse('Appointment successfully.', new AppointmentResource($appointment), 201);
        }

        if($user->is_first_time_appointment == false) {
            $appointment = Appointment::create([
                'user_id' => $id,
                'trainer_id' => $request->trainerId,
                'appointment_date' => $request->appointmentDate,
                'appointment_time' => $request->appointmentTime,
                'appointment_type' => $request->appointmentType,
                "is_approved" => "pending",
                'appointment_fees' => 5000,
            ]);

            return $this->successResponse('Appointment successfully.', new AppointmentResource($appointment), 201);
        }
    }

    public function history($id)
    {
        $appointmentHistory = Appointment::with(['user'])
            ->where('user_id', $id)
            ->orderBy('created_at', 'desc')
            ->paginate(config('pagnation.perPage'));

        if (!$appointmentHistory) {
            return $this->errorResponse('Appointment not found.', 404);
        }

        return $this->successResponse(
            "appointment history Success",
            $this->buildPaginatedResourceResponse(AppointmentResource::class, $appointmentHistory)
        );
    }
}
