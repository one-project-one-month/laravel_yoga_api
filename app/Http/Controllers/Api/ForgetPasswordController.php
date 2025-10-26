<?php

namespace App\Http\Controllers\Api;

use App\Models\User;
use App\Models\UserOtps;
use Illuminate\Http\Request;
use App\Http\Helpers\ApiResponse;
use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Mail;

class ForgetPasswordController extends Controller
{
    use ApiResponse;

    public function sendOtp(Request $request)
    {
        $this->generateOtp($request);
    }

    public function resendOtp(Request $request)
    {
        $this->generateOtp($request);
    }

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

        if(!$user) {
            return $this->errorResponse('User not found', 404);
        }

        if ($user) {
            UserOtps::where('user_id', $user->id)
                ->delete();
        }

        //Ramdom 6-digit OTP
        $otp = rand(100000, 999999);

        UserOtps::create([
            'user_id' => $user->id,
            'otp_code' => $otp,
            'expired_at' => now()->addMinutes(5)
        ]);

        Mail::raw("Your OTP code is: $otp", function ($message) use ($user) {
            $message->to($user->email)
                ->subject('Password Reset OTP');
        });

        return response()->json([
            'status' => true,
            'message' => 'OTP sent to your mail'
        ]);
    }
}
