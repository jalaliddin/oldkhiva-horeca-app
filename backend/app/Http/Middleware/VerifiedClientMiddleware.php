<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

class VerifiedClientMiddleware
{
    public function handle(Request $request, Closure $next): Response
    {
        $user = $request->user();

        if (! $user || $user->role !== 'client') {
            return response()->json([
                'success' => false,
                'message' => 'Ruxsat yo\'q.',
            ], 403);
        }

        if (! $user->is_active) {
            return response()->json([
                'success' => false,
                'message' => 'Hisobingiz hali tasdiqlanmagan. Admin tasdiqlashini kuting.',
            ], 403);
        }

        if (! $user->contract_agreed) {
            return response()->json([
                'success' => false,
                'message' => 'Shartnomani imzolashingiz kerak.',
            ], 403);
        }

        return $next($request);
    }
}
