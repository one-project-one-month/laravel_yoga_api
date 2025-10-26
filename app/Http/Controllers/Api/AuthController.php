<?php

namespace App\Http\Controllers\Api;

use App\Models\User;
use Illuminate\Http\Request;
use App\Http\Helpers\ApiResponse;
use Laravel\Sanctum\HasApiTokens;
use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\Hash;
use Illuminate\Notifications\Notifiable;
use App\Http\Resources\Auth\AuthResource;
use Illuminate\Support\Facades\Validator;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class AuthController extends Controller
{
    use ApiResponse, HasApiTokens, HasFactory, Notifiable;

    /**
     * POST /api/v1/register
     * Register new user
     */
    public function register(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'fullName' => 'required|string',
            'email' => 'required|email|unique:users,email',
            'password' => 'required|min:6|regex:/[0-9]/|regex:/[a-zA-Z]/',
            'confirmPassword' => 'required|same:password'
        ]);

        if ($validator->fails()) {
            return $this->errorResponse($validator->errors(), 422);
        }

        $user = User::create([
            'full_name' => $request->fullName,
            'email' => $request->email,
            'password' => Hash::make($request->password),
        ]);

        $token = $user->createToken('token')->plainTextToken;

        $data = [
            'user' => $user,
            'token' => $token
        ];

        return $this->successResponse('Register Successfully', new AuthResource($data), 201);
    }

    /**
     * POST /api/v1/login
     * user login
     */
    public function login(Request $request){
        $validator = Validator::make($request->all(), [
            'email' => 'required|email',
            'password' => 'required|min:6|regex:/[0-9]/|regex:/[a-zA-Z]/'
        ]);

        if($validator->fails()) {
            return $this->errorResponse($validator->errors(), 422);
        }

        $validateData = $validator->validated();

        $user = User::where('email', $request->email)->first();

        if(!$user) {
            return $this->errorResponse('Invalid email or password. Try again', 404);
        }

        if(!Hash::check($validateData['password'], $user->password)) {
            return $this->errorResponse('Incorrect Password', 401);
        }

        $token = $user->createToken('token')->plainTextToken;

        $data = [
            'user' => $user,
            'token' => $token
        ];

        return $this->successResponse('Login Successfully', new AuthResource($data), 200);
    }

    /**
     * POST /api/v1/logout
     * User logout
     */
    public function logout(Request $request)
    {
        $request->user()->currentAccessToken()->delete();

        return response()->json([
            'success' => true,
            'message' => 'Logged out successfully.'
        ]);
    }
}
