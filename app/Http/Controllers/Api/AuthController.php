<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Http\Helpers\ApiResponse;
use App\Http\Resources\Auth\AuthResource;
use App\Models\User;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Http\Request;
use Illuminate\Notifications\Notifiable;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Validator;
use Illuminate\Support\Carbon;
use Illuminate\Support\Str;
use Laravel\Sanctum\HasApiTokens;

/**
 * @OA\Info(
 *  title="Unlock Wealth Resort API",
 *  version="1.0.0",
 *  description="API documentation for Unlock Wealth Resort",
 * )
 * @OA\Components(
 * @OA\SecurityScheme(
 *  securityScheme="bearerAuth",
 *  type="http",
 *  scheme="bearer",
 *  bearerFormat="JWT",
 *  description="Enter token in format (Bearer <token>)",
 *  in="header",
 *  name="Authorization"
 *  )
 * )
 * @OA\Security(
 * security={
 * {"bearerAuth": {}}
 * }
 * )
 */
class AuthController extends Controller
{
    use ApiResponse, HasApiTokens, HasFactory, Notifiable;

    /**
     * @OA\Post(
     * path="/api/v1/register",
     * summary="Register a new user and return tokens",
     * description="Register user and receive an access token and a refresh token.",
     * tags={"Authentication"},
     * @OA\RequestBody(
     * required=true,
     * @OA\JsonContent(
     * required={"fullName", "email","password", "confirmPassword"},
     * @OA\Property(property="fullName", type="string", example="John Doe"),
     * @OA\Property(property="email", type="string", format="email", example="johndoe@gmail.com"),
     * @OA\Property(property="password", type="string", format="password", example="John123456"),
     * @OA\Property(property="confirmPassword", type="string", format="password", example="John123456"),
     * )
     * ),
     * @OA\Response(
     * response=200,
     * description="Register successful",
     * @OA\JsonContent(
     * @OA\Property(property="message", type="string", example="Register success"),
     * @OA\Property(
     * property="data",
     * type="object",
     * @OA\Property(
     * property="user",
     * type="object",
     * @OA\Property(property="id", type="integer", example=1),
     * @OA\Property(property="name", type="string", example="John Doe"),
     * @OA\Property(property="email", type="string", format="email", example="johndoe@gmail.com")
     * ),
     * @OA\Property(property="accessToken", type="string", example="1|aBcDeFgHiJkLmNoPqRsTuVwXyZ123456"),
     * @OA\Property(
     * property="note",
     * type="string",
     * example="The refresh token is stored in an httpOnly cookie named 'refreshToken'."
     * )
     * )
     * )
     * ),
     * @OA\Response(response=422, description="Validation errors"),
     * @OA\Response(response=500, description="Internal Sever errors"),
     * )
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
     * @OA\Post(
     * path="/api/v1/login",
     * summary="Login user and return tokens",
     * description="Authenticate a user and receive an access token and a refresh token.",
     * tags={"Authentication"},
     * @OA\RequestBody(
     * required=true,
     * description="User credentials",
     * @OA\JsonContent(
     * required={"email","password"},
     * @OA\Property(property="email", type="string", format="email", example="johndoe@gmail.com"),
     * @OA\Property(property="password", type="string", format="password", example="John123456")
     * )
     * ),
     * @OA\Response(
     * response=200,
     * description="Login successful",
     * @OA\JsonContent(
     * @OA\Property(property="message", type="string", example="Login success"),
     * @OA\Property(
     * property="data",
     * type="object",
     * @OA\Property(
     * property="user",
     * type="object",
     * @OA\Property(property="id", type="integer", example=1),
     * @OA\Property(property="name", type="string", example="John Doe"),
     * @OA\Property(property="email", type="string", format="email", example="johndoe@gmail.com")
     * ),
     * @OA\Property(property="accessToken", type="string", example="1|aBcDeFgHiJkLmNoPqRsTuVwXyZ123456"),
     * @OA\Property(
     * property="note",
     * type="string",
     * example="The refresh token is stored in an httpOnly cookie named 'refreshToken'."
     * )
     * )
     * )
     * ),
     * @OA\Response(response=401, description="Unauthorized - invalid password"),
     * @OA\Response(response=404, description="User not found"),
     * @OA\Response(response=422, description="Validation errors")
     * )
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
     * @OA\Post(
     *     path="/api/v1/refresh",
     *     summary="Refresh the access token",
     *     description="Uses a refresh token stored in an httpOnly cookie to generate a new access token.",
     *     tags={"Authentication"},
     *     @OA\Parameter(
     *         name="refreshToken",
     *         in="cookie",
     *         required=true,
     *         description="Refresh token cookie",
     *         @OA\Schema(type="string")
     *     ),
     *     @OA\Response(
     *         response=200,
     *         description="Token refreshed successfully",
     *         @OA\JsonContent(
     *             @OA\Property(property="message", type="string", example="successful"),
     *             @OA\Property(
     *                 property="data",
     *                 type="object",
     *                 @OA\Property(
     *                     property="user",
     *                     type="object",
     *                     @OA\Property(property="id", type="integer", example=1),
     *                     @OA\Property(property="name", type="string", example="John Doe"),
     *                     @OA\Property(property="email", type="string", format="email", example="johndoe@gmail.com")
     *                 ),
     *                 @OA\Property(property="accessToken", type="string", example="2|zYxWvUtSrQpOnMlKjIhGfEdCbA123456")
     *             )
     *         )
     *     ),
     *     @OA\Response(response=401, description="Unauthorized - Invalid or expired refresh token"),
     *     @OA\Response(response=422, description="Validation error - refresh_token is required")
     * )
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
     * @OA\Post(
     * path="/api/v1/logout",
     * summary="Logout user",
     * description="Logs out the current authenticated user by invalidating their token.",
     * tags={"Authentication"},
     * security={{"bearerAuth":{}}},
     * @OA\Response(
     * response=200,
     * description="Logout successful",
     * @OA\JsonContent(
     * @OA\Property(property="message", type="string", example="Logged out successfully")
     * )
     * ),
     * @OA\Response(
     * response=401,
     * description="Unauthenticated"
     * )
     * )
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
