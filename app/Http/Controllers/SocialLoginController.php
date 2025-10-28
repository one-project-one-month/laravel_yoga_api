<?php

namespace App\Http\Controllers;

use App\Models\User;
use Laravel\Socialite\Facades\Socialite;

class SocialLoginController extends Controller
{
    public function redirect($provider) {
        return Socialite::driver($provider)->stateless()->redirect();
    }

    public function callback($provider) {
        $socialLoginData = Socialite::driver($provider)->stateless()->user();

        $user = User::updateOrCreate([
            'provider_id' => $socialLoginData->id,
        ], [
            'full_name' => $socialLoginData->name,
            'email' => $socialLoginData->email,
            'provider' => $provider,
            'provider_id' => $socialLoginData->id,
            'provider_token' => $socialLoginData->token
        ]);

        $token = $user->createToken('token')->plainTextToken;

        return response()->json([
            'user' => $user,
            'token' => $token
        ]);
    }
}
