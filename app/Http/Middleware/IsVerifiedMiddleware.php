<?php

namespace App\Http\Middleware;

use Closure;

class IsVerifiedMiddleware
{
    /**
     * Handle an incoming request.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \Closure  $next
     * @return mixed
     */
    public function handle($request, Closure $next)
    {
        if (!$request->user()->verified) {
            return response()->json(['message' => 'Account unverified'], 403);
        }

        return $next($request);
    }
}
