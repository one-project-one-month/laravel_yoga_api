<?php

namespace App\Http\Controllers\Dashboard;

use App\Http\Controllers\Controller;
use App\Http\Helpers\ApiResponse;
use App\Http\Resources\Dashboard\TrainerResource;
use App\Models\TrainerDetail;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;

/**
 * @OA\Tag(
 * name="Trainers",
 * description="API Endpoints for managing trainers"
 * )
 */
class TrainerController extends Controller
{
    use ApiResponse;

    /**
     * @OA\Get(
     * path="/api/v1/trainers",
     * summary="Get a list of trainers",
     * description="Returns a paginated list of all trainers.",
     * tags={"Trainers"},
     * security={{"bearerAuth":{}}},
     * @OA\Response(
     * response=200,
     * description="Successful operation",
     * @OA\JsonContent(
     * type="object",
     * @OA\Property(property="message", type="string", example="Trainers retrieved successfully"),
     * @OA\Property(property="data", type="object",
     * @OA\Property(property="items", type="array", @OA\Items(ref="#/components/schemas/TrainerResource")),
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
        $trainers = TrainerDetail::with('trainer:id,full_name,email')->paginate(config('pagination.perPage'));

        return $this->successResponse('Trainer retrieved successfully.', $this->buildPaginatedResourceResponse(TrainerResource::class, $trainers), 200);
    }

    /**
     * @OA\Post(
     * path="/api/v1/trainers",
     * summary="Create a new trainer",
     * description="Creates a new trainer account.",
     * tags={"Trainers"},
     * security={{"bearerAuth":{}}},
     * @OA\RequestBody(
     * required=true,
     * @OA\JsonContent(
     * required={"trainerId", "bio", "universityName", "degree", "city", "startDate", "endDate"},
     * @OA\Property(property="trainerId", type="integer", example=1),
     * @OA\Property(property="bio", type="string", example="Lorem, ipsum dolor sit amet consectetur adipisicing elit. Dolorem eos enim corrupti sunt minus nemo cum sint distinctio deserunt fugiat numquam molestiae, adipisci a dolore, fugit esse commodi earum. Quis?"),
     * @OA\Property(property="universityName", type="string", example="Yangon University"),
     * @OA\Property(property="degree", type="string", example="BSc(Physics)"),
     * @OA\Property(property="city", type="string", example="Yangon"),
     * @OA\Property(property="startDate", type="date", example="01-01-20xx"),
     * @OA\Property(property="endDate", type="date", example="01-01-20xx"),
     * )
     * ),
     * @OA\Response(
     * response=201,
     * description="Trainer created successfully",
     * @OA\JsonContent(ref="#/components/schemas/TrainerResource")
     * ),
     * @OA\Response(response=422, description="Validation error"),
     * @OA\Response(response=500, description="Trainer creation failed"),
     * @OA\Response(response=401, description="Unauthenticated")
     * )
     */
    public function store(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'email' => 'required|exists:users,email',
            'bio' => 'required|string',
            'universityName' => 'required|string',
            'degree' => 'required',
            'city' => 'required',
            'startDate' => 'required',
            'endDate' => 'required',
            'salary' => 'nullable|numeric|min:0',
            'branchLocation' => 'nullable|string|max:255',
        ]);

        if ($validator->fails()) {
            return $this->errorResponse($validator->errors(), 422);
        }

        $trainerId = User::where('email', $request->email)->first()->id;

        $trainerDetail = TrainerDetail::create([
            'trainer_id' => $trainerId,
            'bio' => $request->bio,
            'university_name' => $request->universityName,
            'degree' => $request->degree,
            'city' => $request->city,
            'start_date' => $request->startDate,
            'end_date' => $request->endDate
        ]);

        return $this->successResponse('Trainer created successfully.', new TrainerResource($trainerDetail), 201);
    }

    /**
     * @OA\Put(
     * path="/api/v1/trainers/{id}",
     * summary="Update an existing trainer",
     * description="Updates the details of an existing trainer.",
     * tags={"Trainers"},
     * security={{"bearerAuth":{}}},
     * @OA\Parameter(
     * name="id",
     * in="path",
     * required=true,
     * description="ID of the trainer to update",
     * @OA\Schema(type="string")
     * ),
     * @OA\RequestBody(
     * required=true,
     * @OA\JsonContent(
     * required={"trainerId", "bio", "universityName", "degree", "city", "startDate", "endDate"},
     * @OA\Property(property="trainerId", type="integer", example=1),
     * @OA\Property(property="bio", type="string", example="Lorem, ipsum dolor sit amet consectetur adipisicing elit. Dolorem eos enim corrupti sunt minus nemo cum sint distinctio deserunt fugiat numquam molestiae, adipisci a dolore, fugit esse commodi earum. Quis?"),
     * @OA\Property(property="universityName", type="string", example="Yangon University"),
     * @OA\Property(property="degree", type="string", example="BSc(Physics)"),
     * @OA\Property(property="city", type="string", example="Yangon"),
     * @OA\Property(property="startDate", type="date", example="01-01-20xx"),
     * @OA\Property(property="endDate", type="date", example="01-01-20xx"),
     * )
     * ),
     * @OA\Response(
     * response=200,
     * description="Trainer Updated Successfully",
     * @OA\JsonContent(ref="#/components/schemas/TrainerResource")
     * ),
     * @OA\Response(response=422, description="Validation error"),
     * @OA\Response(response=404, description="Trainer not found"),
     * @OA\Response(response=401, description="Unauthenticated")
     * )
     */
    public function update(Request $request, $id)
    {
        $trainer = TrainerDetail::find($id);

        if (!$trainer) {
            return $this->errorResponse('Trainer not found.', 404);
        }

        $validator = Validator::make($request->all(), [
            'email' => 'required|exists:users,email',
            'bio' => 'required|string',
            'universityName' => 'required|string',
            'degree' => 'required',
            'city' => 'required',
            'startDate' => 'required',
            'endDate' => 'required',
            'salary' => 'nullable|numeric|min:0',
            'branchLocation' => 'nullable|string|max:255',
        ]);

        if ($validator->fails()) {
            return $this->errorResponse($validator->errors(), 422);
        }

        $trainerId = User::where('email', $request->email)->first()->id;

        $trainer->update([
            'trainer_id' => $trainerId,
            'bio' => $request->bio,
            'university_name' => $request->universityName,
            'degree' => $request->degree,
            'city' => $request->city,
            'start_date' => $request->startDate,
            'end_date' => $request->endDate
        ]);

        return $this->successResponse('Trainer updated successfully.', new TrainerResource($trainer), 200);
    }
}
