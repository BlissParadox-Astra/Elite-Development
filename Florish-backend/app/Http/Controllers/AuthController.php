<?php

namespace App\Http\Controllers;

use App\Http\Controllers\Controller;
use App\Http\Requests\LoginRequest;
use App\Managers\AuthManager;

class AuthController extends Controller
{
    protected $authManager;

    public function __construct(AuthManager $authManager)
    {
        $this->authManager = $authManager;
    }

    public function login(LoginRequest $request)
    {
        try {
            $response = $this->authManager->login($request);

            return $response;
        } catch (\Exception $e) {
            $errorMessage = $e->getMessage();

            return response()->json(['error' => $errorMessage], 500);
        }
    }

    public function logout()
    {
        try {
            $response = $this->authManager->logout();

            return $response;
        } catch (\Exception $e) {
            $errorMessage = $e->getMessage();

            return response()->json(['error' => $errorMessage], 500);
        }
    }

    public function user()
    {
        $user = auth()->user();

        return response()->json(['user' => $user]);
    }
}
