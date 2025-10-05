<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

class CheckRole
{
    /**
     * Handle an incoming request.
     */
    public function handle(Request $request, Closure $next, ...$roles): Response
    {
        $user = $request->user();

        if (!$user) {
            return response()->json(['message' => 'Unauthenticated.'], 401);
        }

        // Jika user tidak memiliki salah satu role yang diperbolehkan
        if (!in_array($user->role, $roles)) {
            return response()->json(['message' => 'Forbidden: Unauthorized role.'], 403);
        }

        return $next($request);
    }
}
