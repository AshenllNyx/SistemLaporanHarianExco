<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use App\Models\User;
use App\Models\Laporan;

class LoginController extends Controller
{
    /**
     * Show the login form.
     */
    public function showLoginForm()
    {
        return view('login');
    }

    /**
     * Handle a login request.
     */
    public function login(Request $request)
    {
        $credentials = $request->validate([
            'user_name' => ['required', 'string'],
            'password' => ['required', 'string'],
        ]);

        $remember = $request->filled('remember');

        if (Auth::attempt($credentials, $remember)) {
            $request->session()->regenerate();
            
            // Redirect based on user level
            $user = Auth::user();
            if ($user->level === 'admin') {
                return redirect()->intended('/homepage-admin');
            }
            
            return redirect()->intended('/homepage');
        }

        return back()->withErrors([
            'user_name' => 'The provided credentials do not match our records.',
        ])->withInput($request->only('user_name', 'remember'));
    }

    /**
     * Show the registration form.
     */
    public function showRegisterForm()
    {
        return view('register');
    }

    /**
     * Handle a registration request.
     */
    public function register(Request $request)
    {
        $validated = $request->validate([
            'no_ic' => ['required', 'digits:12', 'unique:users,no_ic'],
            'name' => ['required', 'string', 'max:255'],
            'user_name' => ['required', 'string', 'max:255', 'unique:users,user_name'],
            'jantina' => ['required', 'string', 'max:255'],
            'email' => ['required', 'string', 'email', 'max:255', 'unique:users,email'],
            'password' => ['required', 'string', 'min:8', 'confirmed'],
        ]);

        $user = User::create([
            'no_ic' => $validated['no_ic'],
            'name' => $validated['name'],
            'user_name' => $validated['user_name'],
            'jantina' => $validated['jantina'],
            'email' => $validated['email'],
            'password' => Hash::make($validated['password']),
        ]);

        Auth::login($user);
        $request->session()->regenerate();
        return redirect()->intended('/homepage');
    }

   

    /**
     * Log the user out.
     */
    public function logout(Request $request)
    {
        Auth::logout();
        $request->session()->invalidate();
        $request->session()->regenerateToken();
        return redirect('/');
    }
}
