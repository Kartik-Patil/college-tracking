<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use App\Models\User;

class AuthController extends Controller
{
    /**
     * Show login page
     */
    public function showLogin()
    {
        return view('auth.login');
    }

    /**
     * Handle login (USN + DOB)
     */
    public function login(Request $request)
    {
        $request->validate([
            'usn' => 'required|string',
            'dob' => 'required|date'
        ]);

        $user = User::where('usn', $request->usn)
                    ->where('dob', $request->dob)
                    ->where('is_active', 1)
                    ->first();

        if (!$user) {
            return back()->withErrors([
                'login_error' => 'Invalid USN or Date of Birth'
            ]);
        }

        Auth::login($user);

        return redirect()->route('dashboard');
    }

    /**
     * Logout
     */
    public function logout()
    {
        Auth::logout();
        return redirect()->route('login');
    }
}
