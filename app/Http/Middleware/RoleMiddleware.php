<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Symfony\Component\HttpFoundation\Response;

class RoleMiddleware
{
    /**
     * Handle an incoming request.
     */
    public function handle(Request $request, Closure $next, ...$roles): Response
    {
        if (!Auth::check()) {
            return redirect('/login');  // Redirect jika belum login
        }

        $user = Auth::user();
        $userRole = strtolower($user->role);  // Case-insensitive: 'Admin' → 'admin'

        // Unpack multiple roles dari parameter (e.g., 'admin,ketua_tim' → ['admin', 'ketua_tim'])
        $allowedRoles = array_map('strtolower', array_map('trim', explode(',', implode(',', $roles))));

        // Debug log (hapus setelah fix)
        \Log::info('Role Check: User=' . $userRole . ', Allowed=' . implode(', ', $allowedRoles));

        if (!in_array($userRole, $allowedRoles)) {
            \Log::warning('Access Denied: User role "' . $userRole . '" not in [' . implode(', ', $allowedRoles) . ']');
            abort(403, 'Akses ditolak. Role diperlukan: ' . implode(', ', $allowedRoles));
        }

        return $next($request);
    }
}
