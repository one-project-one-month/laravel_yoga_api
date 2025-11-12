<?php

namespace App\Http\Controllers\Client;

use App\Http\Controllers\Controller;
use App\Http\Helpers\ApiResponse;
use App\Http\Resources\Client\AppointmentResource;
use App\Models\Appointment;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;

/**
 * @OA\Tag(
 * name="Client Appointments",
 * description="API Endpoints for managing client appointments"
 * )
 */
class AppointmentController extends Controller
{
    use ApiResponse;

    /**
     * @OA\Post(
     * path="/api/v1/users/{id}/appointments/create",
     * summary="Create user appointment",
     * description="Create user's appointment.",
     * tags={"Client Appointments"},
     * security={{"bearerAuth":{}}},
     * @OA\Parameter(
     * name="id",
     * in="path",
     * required=true,
     * description="ID of the user",
     * @OA\Schema(type="string")
     * ),
     * @OA\RequestBody(
     * required=true,
     * @OA\JsonContent(
     * required={"name", "email", "appointmentDate", "appointmentTime", "appointmentType"},
     * @OA\Property(property="name", type="string", example="John Doe"),
     * @OA\Property(property="email", type="string", format="email", example="johndoe@example.com"),
     * @OA\Property(property="appointmentDate", type="date", example="01-01-20xx"),
     * @OA\Property(property="appointmentTime", type="time", example="08:00"),
     * @OA\Property(property="appointmentType", type="time", example="Healing"),
     * )
     * ),
     * @OA\Response(
     * response=200,
     * description="Appointment Created Successfully",
     * @OA\JsonContent(ref="#/components/schemas/ClientAppointmentResource")
     * ),
     * @OA\Response(response=422, description="Validation error"),
     * @OA\Response(response=401, description="Unauthenticated")
     * )
     */
    public function create(Request $request, $id)
    {
        $validator = Validator::make($request->all(), [
            'name' => 'required',
            'email' => 'required|email',
            'appointmentDate' => 'required|date',
            'appointmentTime' => 'required|date_format:H:i',
            'appointmentType' => 'required'
        ]);

        if ($validator->fails()) {
            return $this->errorResponse($validator->errors(), 422);
        }

        $user = User::find($id, 'id');

        // logger($user);

        if ($user->is_first_time_appointment == true) {
            $appointment = Appointment::create([
                'user_id' => $id,
                'appointment_date' => $request->appointmentDate,
                'appointment_time' => $request->appointmentTime,
                'appointment_type' => $request->appointmentType,
                'is_approved' => 'pending',
                'appointment_fees' => 0
            ]);

            return $this->successResponse('Appointment successfully.', new AppointmentResource($appointment), 201);
        }

        if ($user->is_first_time_appointment == false) {
            $appointment = Appointment::create([
                'user_id' => $id,
                'trainer_id' => $request->trainerId,
                'appointment_date' => $request->appointmentDate,
                'appointment_time' => $request->appointmentTime,
                'appointment_type' => $request->appointmentType,
                'is_approved' => 'pending',
                'appointment_fees' => 5000,
            ]);

            return $this->successResponse('Appointment successfully.', new AppointmentResource($appointment), 201);
        }
    }

    /**
     * @OA\Get(
     * path="/api/v1/users/{id}/appointments/history",
     * summary="Get a history of appointments",
     * description="Returns a paginated list of history appointments.",
     * tags={"Client Appointments"},
     * security={{"bearerAuth":{}}},
     * @OA\Response(
     * response=200,
     * description="Successful operation",
     * @OA\JsonContent(
     * type="object",
     * @OA\Property(property="message", type="string", example="Appointments retrieved successfully"),
     * @OA\Property(property="data", type="object",
     * @OA\Property(property="items", type="array", @OA\Items(ref="#/components/schemas/ClientAppointmentResource")),
     * @OA\Property(property="pagination", type="object",
     * @OA\Property(property="total", type="integer", example=50),
     * @OA\Property(property="per_page", type="integer", example=15),
     * @OA\Property(property="current_page", type="integer", example=1),
     * @OA\Property(property="last_page", type="integer", example=4)
     * )
     * )
     * )
     * ),
     * @OA\Response(response=401, description="Unauthenticated")
     * )
     */
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
            'appointment history Success',
            $this->buildPaginatedResourceResponse(AppointmentResource::class, $appointmentHistory)
        );
    }
}
