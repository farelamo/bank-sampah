<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

class NasabahMiddleware
{
    public function returnCondition($condition, $errorCode, $message)
    {
        return response()->json([
            'success' => $condition,
            'message' => $message,
        ], $errorCode);
    }

    public function handle(Request $request, Closure $next): Response
    {
        if(auth()->user()->role != 'nasabah'){
            return $this->returnCondition(false, 401, 'Invalid role access');
        }
        return $next($request);
    }
}
