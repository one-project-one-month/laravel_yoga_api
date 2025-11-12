<?php

namespace App\Http\Controllers\Dashboard;

use App\Http\Controllers\Controller;
use App\Http\Helpers\ApiResponse;
use App\Http\Resources\Dashboard\AppointmentResource;
use App\Models\Appointment;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;

/**
 * @OA\Tag(
 * name="Appointments",
 * description="API Endpoints for managing appointments"
 * )
 */
class AppointmentController extends Controller
{
    use ApiResponse;

    /**
     * @OA\Get(
     * path="/api/v1/appointments",
     * summary="Get a list of appointments",
     * description="Returns a paginated list of all appointments.",
     * tags={"Appointments"},
     * security={{"bearerAuth":{}}},
     * @OA\Response(
     * response=200,
     * description="Successful operation",
     * @OA\JsonContent(
     * type="object",
     * @OA\Property(property="message", type="string", example="Appointments retrieved successfully"),
     * @OA\Property(property="data", type="object",
     * @OA\Property(property="items", type="array", @OA\Items(ref="#/components/schemas/AppointmentResource")),
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
    public function index()
    {
        $appointments = Appointment::with('user')
            ->orderBy('appointment_date', 'desc')
            ->paginate(config('pagnation.perPage'));

        return $this->successResponse('Appointment retrieved successfully', $this->buildPaginatedResourceResponse(AppointmentResource::class, $appointments), 200);
    }

    /**
     * @OA\Put(
     * path="/api/v1/appointments/{id}",
     * summary="Update an existing appointment",
     * description="Updates the details of an existing appointment.",
     * tags={"Appointments"},
     * security={{"bearerAuth":{}}},
     * @OA\Parameter(
     * name="id",
     * in="path",
     * required=true,
     * description="ID of the appointment to update",
     * @OA\Schema(type="string")
     * ),
     * @OA\RequestBody(
     * required=true,
     * @OA\JsonContent(
     * required={"appointmentDate", "appointmentTime", "appointmentFees", "meetLink", "isApproved", "isCompleted"},
     * @OA\Property(property="appointmentDate", type="date", example="01-01-20xx"),
     * @OA\Property(property="appointmentTime", type="time", example="08:00"),
     * @OA\Property(property="appointmentFees", type="decimal", example=10000.00),
     * @OA\Property(property="meetLink", type="string", example="kdifwu-fkes-ked"),
     * @OA\Property(property="isApproved", type="string", example="accept"),
     * @OA\Property(property="isCompleted", type="boolean", example=false),
     * )
     * ),
     * @OA\Response(
     * response=200,
     * description="Appointment Updated Successfully",
     * @OA\JsonContent(ref="#/components/schemas/AppointmentResource")
     * ),
     * @OA\Response(response=422, description="Validation error"),
     * @OA\Response(response=404, description="Appointment not found"),
     * @OA\Response(response=401, description="Unauthenticated")
     * )
     */
    public function update(Request $request, $id)
    {
        $appointment = Appointment::find($id);
        $user = User::find($appointment->user_id, 'id')->first();

        if (!$appointment) {
            return $this->errorResponse('Appointment not found!', 404);
        }

        $validator = Validator::make($request->all(), [
            'appointmentDate' => 'sometimes|date',
            'appointmentTime' => 'required',
            'appointmentFees' => 'sometimes|numeric|min:0',
            'meetLink' => 'sometimes|string|max:255',
            'isApproved' => 'sometimes|string',
            'isCompleted' => 'sometimes|boolean',
        ]);

        if ($validator->fails()) {
            return $this->errorResponse($validator->errors(), 422);
        }

        if ($request->is_approved == 'accept') {
            $user->update([
                'is_first_time_appointment' => false
            ]);
        }

        $appointment->update($request->only([
            'appointment_date',
            'appointment_time',
            'appointment_fees',
            'meet_link',
            'is_approved',
            'is_completed'
        ]));

        return $this->successResponse('Appointment updated successfully', new AppointmentResource($appointment), 200);
    }

    /**
     * @OA\Delete(
     *     path="/api/v1/appointments/{id}",
     *     summary="Delete a specific appointment",
     *     description="This endpoint permanently deletes a appointment record from the system using its unique ID.",
     *     tags={"Appointments"},
     *     security={{"bearerAuth":{}}},
     *
     *     @OA\Parameter(
     *         name="id",
     *         in="path",
     *         required=true,
     *         description="ID of the appointment to delete",
     *         @OA\Schema(type="integer", example=2)
     *     ),
     *
     *     @OA\Response(
     *         response=200,
     *         description="Appointment deleted successfully",
     *         @OA\JsonContent(
     *             @OA\Property(property="message", type="string", example="Appointment deleted successfully.")
     *         )
     *     ),
     *
     *     @OA\Response(
     *         response=404,
     *         description="Appointment not found",
     *         @OA\JsonContent(
     *             @OA\Property(property="message", type="string", example="Appointment not found.")
     *         )
     *     ),
     *
     *     @OA\Response(
     *         response=500,
     *         description="Internal server error",
     *         @OA\JsonContent(
     *             @OA\Property(property="message", type="string", example="Something went wrong while deleting lesson type.")
     *         )
     *     )
     * )
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
