<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

class CheckCollabTokenMiddleware
{
    /**
     * Handle an incoming request.
     *
     * @param  \Closure(\Illuminate\Http\Request): (\Symfony\Component\HttpFoundation\Response)  $next
     */
    public function handle(Request $request, Closure $next): Response
    {
        if (($token = config('services.collab.hook_token')) && $token != $request->header('X-Angie-WebhookSecret')) {
            return response()->json([
                'success' => false,
                'message' => 'Invalid token'
            ]);
        }
        return $next($request);
    }
}
