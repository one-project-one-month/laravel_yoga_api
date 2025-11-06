<?php

namespace App\Http\Controllers\Api;

use App\Models\User;
use Illuminate\Support\Str;
use Illuminate\Http\Request;
use Illuminate\Support\Carbon;
use App\Http\Helpers\ApiResponse;
use Laravel\Sanctum\HasApiTokens;
use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\Hash;
use Illuminate\Notifications\Notifiable;
use App\Http\Resources\Auth\AuthResource;
use Illuminate\Support\Facades\Log;
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

        try {
            $accessToken = $user->createToken('access-token', ['*'], now()->addHour())->plainTextToken;

            $refreshToken = Str::random(64);

            $user->update([
                'refresh_token' => hash('sha256', $refreshToken),
                'refresh_token_expires_at' => Carbon::now()->addDays(15),
            ]);

            $content = [
                'user' => $user,
                'accessToken' => $accessToken,
            ];

            return $this->successResponse('Login success', new AuthResource($content), 200)->withCookie(cookie(
                'refreshToken',
                $refreshToken,
                60 * 24 * 15,
                '/',
                null,
                app()->isLocal() ? false : true,
                true,
                false,
                'Strict'
            ));
        } catch (\Exception $e) {
            return $this->errorResponse($e->getMessage(), 500);
        }
    }

    /**
     * POST /api/v1/login
     * user login
     */
    public function login(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'email' => 'required|email',
            'password' => 'required|min:6|regex:/[0-9]/|regex:/[a-zA-Z]/'
        ]);

        if ($validator->fails()) {
            return $this->errorResponse($validator->errors(), 422);
        }

        $validateData = $validator->validated();

        $user = User::where('email', $request->email)->first();

        if (!$user) {
            return $this->errorResponse('Invalid email or password. Try again', 404);
        }

        if (!Hash::check($validateData['password'], $user->password)) {
            return $this->errorResponse('Incorrect Password', 401);
        }

        try {
            $accessToken = $user->createToken('access-token', ['*'], now()->addHour())->plainTextToken;

            $refreshToken = Str::random(64);

            $user->update([
                'refresh_token' => hash('sha256', $refreshToken),
                'refresh_token_expires_at' => Carbon::now()->addDays(15),
            ]);

            $content = [
                'user' => $user,
                'accessToken' => $accessToken,
            ];

            return $this->successResponse('Login success', new AuthResource($content), 200)->withCookie(cookie(
                'refreshToken',
                $refreshToken,
                60 * 24 * 15,
                '/',
                null,
                app()->isLocal() ? false : true,
                true,
                false,
                'Strict'
            ));
        } catch (\Exception $e) {
            return $this->errorResponse($e->getMessage(), 500);
        }
    }

    /**
     * POST /api/v1/refresh
     * Refresh access token
     */
    public function refresh(Request $request)
    {
        try {
            $refreshToken = $request->cookie('refreshToken');
            if (!$refreshToken) {
                return $this->errorResponse('Refresh token not found', 401);
            }

            $hashed = hash('sha256', $refreshToken);

            $user = User::where('refresh_token', $hashed)
                ->where('refresh_token_expires_at', '>', now())
                ->first();

            if (!$user) {
                return $this->errorResponse('Invalid refresh token', 401);
            }

            // Rotate refresh token for better security: issue a new refresh token
            $newRefreshToken = Str::random(64);
            $user->update([
                'refresh_token' => hash('sha256', $newRefreshToken),
                'refresh_token_expires_at' => Carbon::now()->addDays(15),
            ]);

            $accessToken = $user->createToken('access-token', ['*'], now()->addHour())->plainTextToken;

            $content = [
                'user' => $user,
                'accessToken' => $accessToken,
            ];

            // Set new refresh token cookie (httpOnly, strict sameSite)
            $cookie = cookie(
                'refreshToken',
                $newRefreshToken,
                60 * 24 * 15,
                '/',
                null,
                app()->isLocal() ? false : true,
                true,
                false,
                'Strict'
            );

            return $this->successResponse('successful', new AuthResource($content), 200)->withCookie($cookie);
        } catch (\Exception $e) {
            Log::error('AuthController@refresh exception: ' . $e->getMessage(), [
                'exception' => $e,
            ]);

            return $this->errorResponse($e->getMessage(), 500);
        }
    }

    /**
     * POST /api/v1/logout
     * User logout
     */
    public function logout(Request $request)
    {
        $user = $request->user();
        $user->tokens()->delete();
        $user->update([
            'refresh_token' => null,
            'refresh_token_expires_at' => null,
        ]);

        $forgetCookie = cookie()->forget('refreshToken');

        return response()->json(['message' => 'Logged out'])->withCookie($forgetCookie);
    }
}
