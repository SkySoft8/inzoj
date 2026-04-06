<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;
use Illuminate\Support\Facades\Auth;

class CheckPremium
{
    /**
     * Handle an incoming request.
     *
     * @param  \Closure(\Illuminate\Http\Request): (\Symfony\Component\HttpFoundation\Response)  $next
     */
    public function handle(Request $request, Closure $next): Response
    {
        $user = Auth::user();

        if ($request->expectsJson()) {
            if (!$user || !$user->is_premium) {
                return response()->json([
                    'success' => false,
                    'message' => 'Premium subscription required to access this resource'
                ], 403);
            }
            return $next($request);
        }

        if (!$user || !$user->is_premium) {
            return redirect()->route('premium');
        }
        
        return $next($request);
    }
}
