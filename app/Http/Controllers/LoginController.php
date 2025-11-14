<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;


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
			return redirect()->intended('/');
		}

		return back()->withErrors([
			'user_name' => 'The provided credentials do not match our records.',
		])->withInput($request->only('user_name', 'remember'));
	}

	/**
	 * Log the user out.
	 */
	public function logout(Request $request)
	{
		Auth::logout();
		$request->session()->invalidate();
		$request->session()->regenerateToken();
		return redirect('/login');
	}
}
