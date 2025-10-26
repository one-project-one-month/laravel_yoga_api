<?php

namespace App\Http\Controllers\Api;

use App\Models\User;
use App\Models\UserOtps;
use Illuminate\Http\Request;
use App\Http\Helpers\ApiResponse;
use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\Mail;

class VerifyEmailController extends Controller
{
    use ApiResponse;

    public function sendEmailVerifyOtp(User $user) {

        $otpCode = rand(100000, 999999);

        UserOtps::create([
            'user_id' => $user->id,
            'otp_code' => $otpCode,
            'expired_at' => now()->addMinutes(5)
        ]);

        Mail::raw("Your verification code is: $otpCode", function($message) use($user){
            $message->to($user->email)
                    ->subject('Verify your email');
        });

        return response()->json([
            'success' => true,
            'message' => 'Verification code sent to your email.'
        ]);
    }

    public function verifyEmail(Request $request) {
        $request->validate([
            'email' => 'required|email',
            'otp' => 'required'
        ]);

        $user = User::where('email', $request->email)->first();

        if(!$user) {
            return $this->errorResponse('User not found', 404);
        }

        $otp = UserOtps::where('user_id', $user->id)
            ->where('otp_code', $request->otp)
            ->where('expired_at', '>', now())
            ->latest()
            ->first();

        if(!$otp) {
            return $this->errorResponse('Invalid OTP code', 400);
        }

        $user->update([
            'is_verified' => true
        ]);

        $otp->delete();

        return response()->json([
            'success' => true,
            'message' => 'Email verified successfully.'
        ]);
    }
}
