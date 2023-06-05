<?php

namespace App\Http\Controllers;

use JWTAuth;
use Exception;
use App\Models\User;
use App\Http\Requests\AuthRequest;

class AuthController extends Controller
{
    public function returnCondition($condition, $errorCode, $message)
    {
        return response()->json([
            'success' => $condition,
            'message' => $message,
        ], $errorCode);
    }

    public function login(AuthRequest $request)
    {
        try {

            $check = User::where('username', $request->username)->first();
            if (!$check) {
                return $this->returnCondition(false, 404, 'username not found');
            }

            if (!$token = auth()->attempt([
                'username' => $request->username,
                'password' => $request->password,
            ])) {
                return $this->returnCondition(false, 401, 'incorrect password');
            }

            return response()->json([
                'success' => true,
                'data' => [
                    'token' => $token,
                    'expires_in' => auth()->factory()->getTTL() * 60,
                ],
            ], 200);
        } catch (Exception $e) {
            return $this->returnCondition(false, 500, 'Internal Server Error');
        }
    }

    public function logout()
    {
        try {
            auth()->logout();

            JWTAuth::getToken();
            JWTAuth::invalidate(true);

            return $this->returnCondition(true, 200, 'Successfully logged out');
        } catch (Exception $e) {
            return $this->returnCondition(false, 500, 'Internal Server Error');
        }
    }
}
