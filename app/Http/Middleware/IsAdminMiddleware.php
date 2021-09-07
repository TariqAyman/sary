<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Illuminate\Http\Response;

class IsAdminMiddleware
{
    /**
     * Handle an incoming request.
     *
     * @param \Illuminate\Http\Request $request
     * @param \Closure $next
     * @return mixed
     */
    public function handle(Request $request, Closure $next)
    {
        if ($request->user()->user_type != 'admin') {
            return response()->json([
                'error' => 'Can\'t Access this Section Admin Only'
            ], Response::HTTP_FORBIDDEN);
        }

        return $next($request);
    }
}
