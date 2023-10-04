<?php

namespace App\Http\Controllers;

use App\Http\Controllers\Controller;
use App\Models\UserCredential;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Cookie;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Log;


class AuthController extends Controller
{
    public function login(Request $request)
    {
        $request->validate([
            'username' => 'required|string',
            'password' => 'required|string',
        ]);


        $credentials = $request->only('username', 'password');

        $userCredential = UserCredential::where('username', $credentials['username'])->first();

        if (!$userCredential || !Hash::check($credentials['password'], $userCredential->password)) {
            return response()->json(['error' => 'Invalid credentials'], 401);
        }

        $user = $userCredential->user;

        $token = $user->createToken('token')->plainTextToken;

        Log::info('Generated Token: ' . $token);

        $userType = $user->userType->user_type;


        $cookie = cookie('token', $token, 60 * 24); // 1 day


        return response([
            'message' => 'Logged in successfully',
            'user' => $user,
            'userType' => $userType,
            'token' => $token,
        ])->withCookie($cookie);
    }

    public function user()
    {
        $user = auth()->user();

        return response()->json(['user' => $user]);
    }

    public function logout(Request $request)
    {
        $cookie = Cookie::forget('token');


        return response([
            'message' => 'Logged out successfully',
        ])->withCookie($cookie);
    }
}
