<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

/**
 * Admin Module: Middleware untuk memastikan user yang mengakses adalah admin.
 *
 * User dianggap admin jika kolom `role` pada tabel users bernilai 'admin'.
 */
class EnsureUserIsAdmin
{
    /**
     * Handle an incoming request.
     *
     * @param  \Closure(\Illuminate\Http\Request): (\Symfony\Component\HttpFoundation\Response)  $next
     */
    public function handle(Request $request, Closure $next): Response
    {
        $user = $request->user();

        $isAdmin = $user && (
            $user->role === 'admin' ||
            (method_exists($user, 'isAdmin') && $user->isAdmin())
        );

        if (! $isAdmin) {
            if ($request->expectsJson()) {
                return response()->json([
                    'message' => 'Akses ditolak. Anda bukan admin.',
                ], 403);
            }

            return redirect()->route('admin.login')
                ->with('error', 'Akses ditolak. Silakan login sebagai admin.');
        }

        return $next($request);
    }
}
