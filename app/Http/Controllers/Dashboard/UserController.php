<?php

namespace App\Http\Controllers\Dashboard;

use App\Http\Controllers\Controller;
use App\Http\Helpers\ApiResponse;
use App\Http\Resources\Dashboard\UserResource;
use App\Models\User;
use CloudinaryLabs\CloudinaryLaravel\Facades\Cloudinary;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Http\Request;
use Illuminate\Notifications\Notifiable;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Validator;
use Laravel\Sanctum\HasApiTokens;

/**
 * @OA\Tag(
 * name="Users",
 * description="API Endpoints for managing users"
 * )
 */
class UserController extends Controller
{
    use ApiResponse, HasApiTokens, HasFactory, Notifiable;

    /**
     * @OA\Get(
     * path="/api/v1/users",
     * summary="Get a list of users",
     * description="Returns a paginated list of all users.",
     * tags={"Users"},
     * security={{"bearerAuth":{}}},
     * @OA\Response(
     * response=200,
     * description="Successful operation",
     * @OA\JsonContent(
     * type="object",
     * @OA\Property(property="message", type="string", example="Users retrieved successfully"),
     * @OA\Property(property="data", type="object",
     * @OA\Property(property="items", type="array", @OA\Items(ref="#/components/schemas/UserResource")),
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
        $users = User::with(['role', 'trainerDetails'])->paginate(config('pagination.perPage'));

        return $this->successResponse('User retrieved successfully', $this->buildPaginatedResourceResponse(UserResource::class, $users), 200);
    }

    /**
     * @OA\Post(
     * path="/api/v1/users",
     * summary="Create a new user",
     * description="Creates a new user account.",
     * tags={"Users"},
     * security={{"bearerAuth":{}}},
     * @OA\RequestBody(
     * required=true,
     * @OA\JsonContent(
     * required={"fullName", "nickname", "email", "password", "address", "profile", "dateOfBirth", "placeOfBirth", "weight", "telegramPh", "whatsappPh"},
     * @OA\Property(property="fullName", type="string", example="John Doe"),
     * @OA\Property(property="nickName", type="string", example="John"),
     * @OA\Property(property="email", type="string", format="email", example="johndoe@example.com"),
     * @OA\Property(property="password", type="string", format="password", example="Password123"),
     * @OA\Property(property="address", type="string", example="Rose Mary Street"),
     * @OA\Property(property="profile", type="string", example="helloworld.jpg"),
     * @OA\Property(property="dateOfBirth", type="date", example="01-01-1000"),
     * @OA\Property(property="placeOfBirth", type="string", example="Yangon"),
     * @OA\Property(property="weight", type="integer", example="56"),
     * @OA\Property(property="telegramPh", type="string", example="09xxxxxxxxx"),
     * @OA\Property(property="whatsappPh", type="string", example="09xxxxxxxxx"),
     * )
     * ),
     * @OA\Response(
     * response=201,
     * description="User created successfully",
     * @OA\JsonContent(ref="#/components/schemas/UserResource")
     * ),
     * @OA\Response(response=422, description="Validation error"),
     * @OA\Response(response=500, description="User creation failed"),
     * @OA\Response(response=401, description="Unauthenticated")
     * )
     */
    public function store(Request $request)
    {
        $validator = Validator::make(
            $request->all(),
            [
                'fullName' => 'required|string|max:255',
                'nickName' => 'required|string|max:255',
                'email' => 'required|email|unique:users|max:255',
                'password' => 'required|min:6|regex:/[0-9]/|regex:/[a-zA-Z]/',
                'address' => 'required',
                'profile' => 'required|mimes:jpg,png,jpeg,heic,svg',
                'dateOfBirth' => 'required|date',
                'placeOfBirth' => 'required',
                'weight' => 'required',
                'telegramPh' => 'required|numeric|digits_between:7,15',
                'whatsappPh' => 'required|numeric|digits_between:7,15',
            ]
        );

        if ($validator->fails()) {
            return $this->errorResponse($validator->errors(), 422);
        }

        if ($request->hasFile('profile')) {
            $uploadedFile = Cloudinary::upload($request->file('profile')->getRealPath(), ['folder' => 'profiles'])->getSecurePath();
            $profileUrl = $uploadedFile['secure_url'];
            $profilePublicId = $uploadedFile['public_id'];
        }

        try {
            $user = User::create([
                'full_name' => $request->fullName,
                'nick_name' => $request->nickName,
                'email' => $request->email,
                'password' => Hash::make($request->password),
                'role_id' => 3,
                'address' => $request->address,
                'profile_url' => $profileUrl,
                'date_of_birth' => $request->dateOfBirth,
                'place_of_birth' => $request->placeOfBirth,
                'weight' => $request->weight,
                'ph_no_telegram' => $request->telegramPh,
                'ph_no_whatsapp' => $request->whatsappPh,
                'daily_routine_for_weekly' => $request->dailyRoutine,
                'special_request' => $request->specialRequest,
            ]);

            return $this->successResponse('User created successfully', new UserResource($user), 201);
        } catch (\Exception $e) {
            return $this->errorResponse('User creation failed:' . $e->getMessage(), 500);
        }
    }

    /**
     * @OA\Get(
     * path="/api/v1/users/{id}",
     * summary="Get a single user",
     * description="Returns the details of a specific user by their ID.",
     * tags={"Users"},
     * security={{"bearerAuth":{}}},
     * @OA\Parameter(
     * name="id",
     * in="path",
     * required=true,
     * description="ID of the user",
     * @OA\Schema(type="string")
     * ),
     * @OA\Response(
     * response=200,
     * description="User Fetched Successfully",
     * @OA\JsonContent(ref="#/components/schemas/UserResource")
     * ),
     * @OA\Response(response=404, description="User not found"),
     * @OA\Response(response=401, description="Unauthenticated")
     * )
     */
    public function show($id)
    {
        // logger($user);
        $user = User::find($id);
        $user->load(['role', 'trainerDetails']);

        if (!$user) {
            return $this->errorResponse('User not found.', 404);
        }

        return $this->successResponse('User Fetched Successfully', new UserResource($user), 200);
    }

    /**
     * @OA\Put(
     * path="/api/v1/users/{id}",
     * summary="Update an existing user",
     * description="Updates the details of an existing user.",
     * tags={"Users"},
     * security={{"bearerAuth":{}}},
     * @OA\Parameter(
     * name="id",
     * in="path",
     * required=true,
     * description="ID of the user to update",
     * @OA\Schema(type="string")
     * ),
     * @OA\RequestBody(
     * required=true,
     * @OA\JsonContent(
     * required={"fullName", "nickname", "email", "address", "dateOfBirth", "placeOfBirth", "weight"},
     * @OA\Property(property="fullName", type="string", example="John Doe"),
     * @OA\Property(property="nickName", type="string", example="John"),
     * @OA\Property(property="email", type="string", format="email", example="johndoe@example.com"),
     * @OA\Property(property="address", type="string", example="Rose Mary Street"),
     * @OA\Property(property="dateOfBirth", type="string", example="01-01-1000"),
     * @OA\Property(property="placeOfBirth", type="string", example="Yangon"),
     * @OA\Property(property="weight", type="integer", example="56"),
     * )
     * ),
     * @OA\Response(
     * response=200,
     * description="User Updated Successfully",
     * @OA\JsonContent(ref="#/components/schemas/UserResource")
     * ),
     * @OA\Response(response=422, description="Validation error"),
     * @OA\Response(response=404, description="User not found"),
     * @OA\Response(response=401, description="Unauthenticated")
     * )
     */
    public function update(Request $request, $id)
    {
        $user = User::find($id);

        if (!$user) {
            return $this->errorResponse('User not found', 404);
        }

        $validator = Validator::make($request->all(), [
            'fullName' => 'required|string|max:255',
            'nickName' => 'required|string|max:255',
            'email' => 'required|email|unique:users,email,' . $id,
            'password' => 'nullable|min:6|regex:/[0-9]/|regex:/[a-zA-Z]/',
            'confirmPassword' => 'same:password',
            'address' => 'required',
            'profile' => 'nullable|mimes:jpg,jpeg,png,svg,gif,heic',
            'dateOfBirth' => 'required|date',
            'placeOfBirth' => 'required',
            'weight' => 'required',
            'ph_no_telegram' => 'nullable|numeric|digits_between:7,15',
            'ph_no_whatsapp' => 'nullable|numeric|digits_between:7,15',
        ]);

        if ($validator->fails()) {
            return $this->errorResponse($validator->errors(), 422);
        }

        $userData = [
            'full_name' => $request->fullName,
            'nick_name' => $request->nickName,
            'email' => $request->email,
            'role_id' => 3,
            'address' => $request->address,
            'date_of_birth' => $request->dateOfBirth,
            'place_of_birth' => $request->placeOfBirth,
            'weight' => $request->weight,
            'ph_no_telegram' => $request->telegramPh,
            'ph_no_whatsapp' => $request->whatsappPH,
            'daily_routine_for_weekly' => $request->dailyRoutine,
            'special_request' => $request->specialRequest,
        ];

        if ($request->password) {
            $user['password'] = Hash::make($request->password);
        }

        if ($request->hasFile($request->profile)) {
            if ($user->profile_public_id) {
                Cloudinary::destroy($user->profile_public_id);
            }

            $uploadedFile = Cloudinary::upload($request->file('profile')->getRealPath(), ['folder' => 'profiles'])->getSecurePath();
            $userData['profile_url'] = $uploadedFile['secure_url'];
            $userData['profile_public_id'] = $uploadedFile['public_id'];
        }

        try {
            $user->update($userData);

            return $this->successResponse('Account updated sucesssfully', new UserResource($user));
        } catch (\Exception $e) {
            return $this->errorResponse('User updated failed:' . $e->getMessage(), 500);
        }
    }
}
