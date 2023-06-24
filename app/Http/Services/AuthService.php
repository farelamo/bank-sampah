<?php

namespace App\Services;

use Exception;


class AuthService
{
    public function returnCondition($condition, $errorCode, $message)
    {
        return response()->json([
            'success' => $condition,
            'message' => $message,
        ], $errorCode);
    }

    public function me()
    {
        try {

            return response()->json([
                'success' => true,
                'data'    => auth()->user(),
            ], 200);
        } catch (Exception $e) {
            return $this->returnCondition(false, 500, 'Internal Server Error');
        }
    }
}
