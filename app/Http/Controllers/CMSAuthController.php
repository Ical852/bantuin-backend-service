<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class CMSAuthController extends Controller
{
    public function index()
    {
        return view('auth.login');
    }

    public function login(Request $request)
    {
        $credentials = $request->validate([
            'email' => ['required',  'email:dns'],
            'password' => ['required']
        ]);

        if(Auth::attempt($credentials)){
            $request->session()->regenerate();

            if(!Auth::user()->email_verified_at) {
                Auth::logout();
                request()->session()->invalidate();
                request()->session()->regenerateToken();

                return back()->with('failed', 'Verify your email!');
            }

            $user = Auth::user()->role;

            if($user != 'admin') {
                Auth::logout();
                request()->session()->invalidate();
                request()->session()->regenerateToken();

                return back()->with('failed', 'You are not Allowed!');
            }

            return redirect('/cms');
        }

        return back()->with('failed', 'Login failed!');
    }

    public function logout()
    {
        Auth::logout();

        request()->session()->invalidate();

        request()->session()->regenerateToken();

        return redirect('/auth');
    }
}
