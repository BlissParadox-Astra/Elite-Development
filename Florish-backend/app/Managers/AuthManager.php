<?php

namespace App\Managers;

use App\Http\Requests\LoginRequest;
use App\Models\UserCredential;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Cookie;

class AuthManager
{
    public function login(LoginRequest $request)
    {
        $credentials = $request->only('username', 'password');

        $userCredential = UserCredential::where('username', $credentials['username'])->first();

        if (!$userCredential || !Hash::check($credentials['password'], $userCredential->password)) {
            return response()->json(['error' => 'Invalid credentials'], 401);
        } else {
            Log::info('User: ' . $userCredential->user_id . ' logged in');
        }

        $user = $userCredential->user;

        $token = $user->createToken('token')->plainTextToken;

        Log::info('Generated Token: ' . $token);

        $userType = $user->userType->user_type;

        $cookie = cookie('token', $token, 60 * 24);

        return response([
            'message' => 'Logged in successfully',
            'user' => $user,
            'userType' => $userType,
            'token' => $token,
        ])->withCookie($cookie);
    }

    public function logout()
    {
        $user = auth()->user();
        $user->tokens->each(function ($token, $key) {
            $token->delete();
        });
        $cookie = Cookie::forget('token');

        return response([
            'message' => 'Logged out successfully',
        ])->withCookie($cookie);
    }
}
