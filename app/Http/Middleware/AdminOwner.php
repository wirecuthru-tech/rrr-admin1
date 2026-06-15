<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

class AdminOwner
{
    public function handle(Request $request, Closure $next): Response
    {
        if (!session()->has('admin_owner')) {
            return redirect()->route('admin.login');
        }

        if (session('admin_role') !== 'owner') {
            session()->forget([
                'admin_owner',
                'admin_name',
                'admin_email',
                'admin_role'
            ]);

            return redirect()
                ->route('admin.login')
                ->with('error', 'Sirf owner admin panel access kar sakta hai');
        }

        return $next($request);
    }
}