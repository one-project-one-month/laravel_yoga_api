<?php

namespace App\Http\Controllers\Dashboard;

use App\Models\User;
use CloudinaryLabs\CloudinaryLaravel\Facades\Cloudinary;
use Illuminate\Http\Request;
use App\Http\Helpers\ApiResponse;
use Laravel\Sanctum\HasApiTokens;
use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\Hash;
use Illuminate\Notifications\Notifiable;
use Illuminate\Support\Facades\Validator;
use App\Http\Resources\Dashboard\UserResource;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class UserController extends Controller
{
    use ApiResponse, HasApiTokens, HasFactory, Notifiable;

    /**
     * GET /api/v1/users
     * List all user
     */
    public function index()
    {
        $users = User::with(['role', 'trainerDetails'])->paginate(config('pagination.perPage'));

        return $this->successResponse('User retrieved successfully', $this->buildPaginatedResourceResponse(UserResource::class, $users), 200);
    }

    /**
     * POST /api/v1/users
     * Create new user
     * Admin only
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
                'profile' => 'required',
                'dateOfBirth' => 'required|date',
                'placeOfBirth' => 'required',
                'weight' => 'required',
                'ph_no_telegram' => 'required|numeric|digits_between:7,15',
                'ph_no_whatsapp' => 'required|numeric|digits_between:7,15',
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
                'profile_public_id' => $profilePublicId,
                'date_of_birth' => $request->dateOfBirth,
                'place_of_birth' => $request->placeOfBirth,
                'weight' => $request->weight,
                'ph_no_telegram' => $request->telegramPh,
                'ph_no_whatsapp' => $request->whatsappPH,
                'daily_routine_for_weekly' => $request->dailyRoutine,
                'special_request' => $request->specialRequest,
            ]);

            return $this->successResponse('User created successfully', new UserResource($user), 201);

        } catch (\Exception $e) {
            return $this->errorResponse('User creation failed:' . $e->getMessage(), 500);
        }
    }

    /**
     * GET /api/v1/users/{id}
     * Show user
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
     * PUT /api/v1/users/{id}
     * Update user
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
