<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Symfony\Component\HttpFoundation\Response;

class RoleMiddleware
{
    public function handle(Request $request, Closure $next, ...$roles): Response
    {
        if (!Auth::check()) {
            return redirect('/login');
        }
        $user = Auth::user();
        $userRole = strtolower($user->role ?? 'guest');
        $allowed = array_map('strtolower', array_map('trim', explode(',', implode(',', $roles))));
        if (!in_array($userRole, $allowed)) {
            abort(403, 'Akses ditolak. Role diperlukan: ' . implode(', ', $allowed));
        }
        return $next($request);
    }
}
