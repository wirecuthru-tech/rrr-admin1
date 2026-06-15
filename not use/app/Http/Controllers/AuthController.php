<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;

class AuthController extends Controller
{
    public function login(Request $request)
    {
        if (
            $request->email == 'admin@test.com' &&
            $request->password == '123456'
        ) {

            session([
                'admin' => true
            ]);

            return redirect('/admin');
        }

        return back();
    }

    public function logout()
    {
        session()->flush();

        return redirect('/login');
    }
}