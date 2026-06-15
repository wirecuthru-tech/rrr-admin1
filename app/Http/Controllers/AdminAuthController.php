<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;

class AdminAuthController extends Controller
{
    public function showLogin()
    {
        return view('admin.login');
    }

    public function login(Request $request)
    {
        $request->validate([
            'email' => 'required',
            'password' => 'required',
        ]);

        $owner = DB::connection('mongodb')
            ->table('users')
            ->where('email', $request->email)
            ->where('role', 'owner')
            ->first();

        if (!$owner) {
            return back()->with('error', 'Sirf owner login kar sakta hai');
        }

        if (!Hash::check($request->password, $owner->password ?? '')) {
            return back()->with('error', 'Email ya password galat hai');
        }

        session([
    'admin_owner' => true,
    'admin_name' => $owner->name ?? 'Owner',
    'admin_email' => $owner->email ?? '',
    'admin_role' => $owner->role ?? 'owner',
]);

        return redirect()->route('admin.dashboard');
    }

    public function logout()
    {
        session()->forget(['admin_owner', 'admin_name', 'admin_email']);

        return redirect()->route('admin.login');
    }
}