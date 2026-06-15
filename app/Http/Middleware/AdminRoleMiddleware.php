<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class AdminRoleMiddleware
{
    public function handle(Request $request, Closure $next, ...$roles)
    {
        $uid = $request->header('X-User-Id') ?: $request->input('user_id') ?: $request->input('firebase_uid');
        if (!$uid) {
            return response()->json(['success' => false, 'message' => 'Unauthorized'], 401);
        }

        $user = DB::connection('mongodb')->table('users')
            ->where('firebase_uid', $uid)
            ->orWhere('real_id', $uid)
            ->orWhere('_id', $uid)
            ->first();

        $userRoles = (array)($user->roles ?? $user->role ?? []);
        $allowed = empty($roles) || count(array_intersect($roles, $userRoles)) > 0 || in_array('superadmin', $userRoles, true);

        if (!$allowed) {
            return response()->json(['success' => false, 'message' => 'Forbidden'], 403);
        }

        return $next($request);
    }
}
