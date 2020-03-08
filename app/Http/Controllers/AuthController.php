<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;

class AuthController extends Controller
{
    /**
     * Serves the login page.
     */
    public function login()
    {
        return view('pages.login');
    }

    /**
     * Handles authentication attempts.
     */
    public function authenticate(Request $request)
    {
        $credentials = $request->validate([
            'email' => 'required|email', 
            'password' => 'required',
            'remember_me' => 'sometimes|accepted'
        ]);

        if (\Auth::attempt($request->only('email', 'password'), array_key_exists('remember_me', $credentials))) {
            return redirect()->intended(route('overview'));
        } else {
            return redirect()->back()->withErrors(['auth' => 'Wrong credentials.']);
        }
    }

    /**
     * Terminates the auth session for the user.
     */
    public function logout()
    {
        \Auth::logout();
        return redirect()->route('login');
    }
}
