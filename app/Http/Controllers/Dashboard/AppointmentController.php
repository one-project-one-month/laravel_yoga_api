<?php

namespace App\Http\Controllers\Dashboard;

use App\Models\User;
use App\Models\Appointment;
use Illuminate\Http\Request;
use App\Http\Helpers\ApiResponse;
use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\Validator;
use App\Http\Resources\Dashboard\AppointmentResource;

class AppointmentController extends Controller
{
    use ApiResponse;
    /**
     * GET /api/appointments
     * List all appointments
     */
    public function index()
    {
        $appointments = Appointment::with('user')
            ->orderBy('appointment_date', 'desc')
            ->paginate(config('pagnation.perPage'));

        return $this->successResponse('Appointment retrieved successfully', $this->buildPaginatedResourceResponse(AppointmentResource::class, $appointments), 200);
    }

    /**
     * PUT /api/appointments/{id}
     * Update appointment (approve / complete / edit)
     */
    public function update(Request $request, $id)
    {
        $appointment = Appointment::find($id);
        $user = User::find($appointment->user_id, 'id')->first();

        if (!$appointment) {
            return $this->errorResponse('Appointment not found!', 404);
        }

        $validator = Validator::make($request->all(), [
            'appointment_date' => 'sometimes|date',
            'appointment_fees' => 'sometimes|numeric|min:0',
            'meet_link' => 'sometimes|string|max:255',
            'is_approved' => 'sometimes|string',
            'is_completed' => 'sometimes|boolean',
        ]);

        if ($validator->fails()) {
            return $this->errorResponse($validator->errors(), 422);
        }

        if($request->is_approved == "accept") {
            $user->update([
                'is_first_time_appointment' => false
            ]);
        }

        $appointment->update($request->only([
            'appointment_date',
            'appointment_fees',
            'meet_link',
            'is_approved',
            'is_completed'
        ]));

        return $this->successResponse('Appointment updated successfully', new AppointmentResource($appointment), 200);
    }

    /**
     * DELETE /api/appointments/{id}
     * Delete appointment
     */
    public function destroy($id)
    {
        $appointment = Appointment::find($id);

        if (!$appointment) {
            return $this->errorResponse('Appointment not found!', 404);
        }

        $appointment->delete();

        return $this->successResponse('Appointment deleted successdfully', new AppointmentResource($appointment), 204);
    }
}
