<?php

namespace App\Http\Controllers;

use App\Http\Controllers\Controller;
use App\Models\UserCredential;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use Illuminate\Validation\ValidationException;

class AuthController extends Controller
{
    public function login(Request $request)
    {
        try {
            $credentials = $request->only('username', 'password');

            $userCredential = UserCredential::where('username', $credentials['username'])->first();

            if (!$userCredential || !Hash::check($credentials['password'], $userCredential->password)) {
                throw ValidationException::withMessages(['message' => 'Invalid credentials']);
            }

            $user = $userCredential->user;

            $token = $user->createToken('authToken')->plainTextToken;
            
            $userType = $user->userType->user_type;

            return response()->json(['token' => $token, 'userType' => $userType]);
        } catch (\Exception $e) {
            return response()->json(['error' => 'Invalid credentials'], 422);
        }
    }

    public function logout(Request $request)
    {
        try {
            $user = $request->user();
            $user->tokens()->delete();

            return response()->json(['message' => 'Logged out successfully']);
        } catch (\Exception $e) {
            $errorMessage = $e->getMessage();

            return response()->json(['error' => $errorMessage], 500);
        }
    }
}
