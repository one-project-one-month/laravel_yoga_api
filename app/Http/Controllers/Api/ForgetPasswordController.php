<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Http\Helpers\ApiResponse;
use App\Models\User;
use App\Models\UserOtps;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Mail;

class ForgetPasswordController extends Controller
{
    use ApiResponse;

    /**
     * @OA\Post(
     *     path="/api/v1/forget-password",
     *     summary="Send OTP to user's email for password reset",
     *     description="This endpoint sends a one-time password (OTP) to the user's registered email to verify their identity before allowing a password reset.",
     *     tags={"Authentication"},
     *
     *     @OA\RequestBody(
     *         required=true,
     *         @OA\JsonContent(
     *             required={"email"},
     *             @OA\Property(property="email", type="string", format="email", example="user@example.com")
     *         )
     *     ),
     *
     *     @OA\Response(
     *         response=200,
     *         description="OTP sent successfully",
     *         @OA\JsonContent(
     *             @OA\Property(property="message", type="string", example="An OTP has been sent to your email address.")
     *         )
     *     ),
     *     @OA\Response(
     *         response=404,
     *         description="User not found",
     *         @OA\JsonContent(
     *             @OA\Property(property="message", type="string", example="We can't find a user with that email address.")
     *         )
     *     )
     * )
     */
    public function sendOtp(Request $request)
    {
        $this->generateOtp($request);
    }

    /**
     * @OA\Post(
     *     path="/api/v1/resend-otp",
     *     summary="Send OTP again to user's email for password reset",
     *     description="This endpoint sends ahain a one-time password (OTP) to the user's registered email to verify their identity before allowing a password reset.",
     *     tags={"Authentication"},
     *
     *     @OA\RequestBody(
     *         required=true,
     *         @OA\JsonContent(
     *             required={"email"},
     *             @OA\Property(property="email", type="string", format="email", example="user@example.com")
     *         )
     *     ),
     *
     *     @OA\Response(
     *         response=200,
     *         description="OTP sent successfully",
     *         @OA\JsonContent(
     *             @OA\Property(property="message", type="string", example="An OTP has been sent to your email address.")
     *         )
     *     ),
     *     @OA\Response(
     *         response=404,
     *         description="User not found",
     *         @OA\JsonContent(
     *             @OA\Property(property="message", type="string", example="We can't find a user with that email address.")
     *         )
     *     )
     * )
     */
    public function resendOtp(Request $request)
    {
        $this->generateOtp($request);
    }

    /**
     * @OA\Post(
     *     path="/api/v1/verify-otp",
     *     summary="Verify OTP before allowing password reset",
     *     description="This endpoint verifies the OTP sent to the user's email or phone. If the OTP is valid and not expired, the system will allow the user to proceed to the password reset step.",
     *     tags={"Authentication"},
     *
     *     @OA\RequestBody(
     *         required=true,
     *         @OA\JsonContent(
     *             required={"email", "otp"},
     *             @OA\Property(property="email", type="string", format="email", example="user@example.com"),
     *             @OA\Property(property="otp", type="string", example="123456")
     *         )
     *     ),
     *
     *     @OA\Response(
     *         response=200,
     *         description="OTP verified successfully",
     *         @OA\JsonContent(
     *             @OA\Property(property="message", type="string", example="OTP verified. You can now reset your password."),
     *             @OA\Property(property="accessToken", type="string", example="1|aBcDeFgHiJkLmNoPqRsTuVwXyZ123456")
     *         )
     *     ),
     *     @OA\Response(
     *         response=400,
     *         description="Invalid or expired OTP",
     *         @OA\JsonContent(
     *             @OA\Property(property="message", type="string", example="The OTP is invalid or has expired.")
     *         )
     *     )
     * )
     */
    public function verifyOtp(Request $request)
    {
        $request->validate([
            'email' => 'required|email',
            'otp' => 'required',
        ]);

        $user = User::where('email', $request->email)->first();

        if (!$user) {
            return $this->errorResponse('User not found', '404');
        }

        $otpData = UserOtps::where('user_id', $user->id)
            ->where('otp_code', $request->otp)
            ->where('expired_at', '>', now())
            ->latest()
            ->first();

        if (!$otpData) {
            return $this->errorResponse('Invalid OTP code', '400');
        }

        $token = $user->createToken('token')->plainTextToken;

        $otpData->update(['token' => $token]);

        return response()->json([
            'success' => true,
            'message' => 'OTP verified successfully.',
            'token' => $token
        ], 200);
    }

    /**
     * @OA\Post(
     *     path="/api/v1/reset-password",
     *     summary="Reset password after successful OTP verification",
     *     description="This endpoint allows the user to reset their password after the OTP has been successfully verified. The temporary token obtained from OTP verification should be included in this request.",
     *     tags={"Authentication"},
     *
     *     @OA\RequestBody(
     *         required=true,
     *         @OA\JsonContent(
     *             required={"token", "password"},
     *             @OA\Property(property="accessToken", type="string", example="1|aBcDeFgHiJkLmNoPqRsTuVwXyZ123456"),
     *             @OA\Property(property="password", type="string", format="password", example="NewStrongPassword123"),
     *         )
     *     ),
     *
     *     @OA\Response(
     *         response=200,
     *         description="Password reset successful",
     *         @OA\JsonContent(
     *             @OA\Property(property="message", type="string", example="Password has been reset successfully.")
     *         )
     *     ),
     *     @OA\Response(
     *         response=400,
     *         description="Invalid token mismatch",
     *         @OA\JsonContent(
     *             @OA\Property(property="message", type="string", example="Invalid token do not match.")
     *         )
     *     )
     * )
     */
    public function resetPassword(Request $request)
    {
        $request->validate([
            'token' => 'required',
            'password' => 'required|min:6|regex:/[0-9]/|regex:/[a-zA-Z]/'
        ]);

        $otp = UserOtps::where('token', $request->token)
            ->where('expired_at', '>', now())
            ->latest()
            ->first();

        if (!$otp) {
            return $this->errorResponse('Invalid Token', 400);
        }

        $user = User::find($otp->user_id, 'id');

        if (!$user) {
            return $this->errorResponse('User not found', 404);
        }

        $user->update([
            'password' => Hash::make($request->password)
        ]);

        $user->tokens()->delete();

        $otp->delete();

        return response()->json([
            'success' => true,
            'message' => 'Password reset successfully.'
        ], 200);
    }

    private function generateOtp(Request $request)
    {
        $request->validate([
            'email' => 'required|email|exists:users,email'
        ]);

        $user = User::where('email', $request->email)->first();

        if (!$user) {
            return $this->errorResponse('User not found', 404);
        }

        if ($user) {
            UserOtps::where('user_id', $user->id)
                ->delete();
        }

        // Ramdom 6-digit OTP
        $otp = rand(100000, 999999);

        UserOtps::create([
            'user_id' => $user->id,
            'otp_code' => $otp,
            'expired_at' => now()->addMinutes(5)
        ]);

        Mail::raw("Your OTP code is: $otp", function ($message) use ($user) {
            $message
                ->to($user->email)
                ->subject('Password Reset OTP');
        });

        return response()->json([
            'status' => true,
            'message' => 'OTP sent to your mail'
        ]);
    }
}
