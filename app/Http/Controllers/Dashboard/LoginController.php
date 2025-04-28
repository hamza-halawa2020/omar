<?php

namespace App\Http\Controllers\Dashboard;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\RateLimiter;
use Illuminate\Support\Facades\Log;

class LoginController extends Controller
{
   
    public function showLoginForm()
    {
        return view('dashboard.auth.index');
    }


    public function login(Request $request)
    {
        $this->validateLogin($request);

        $key = 'login-attempts:' . $request->ip();
        if (RateLimiter::tooManyAttempts($key, 5)) {
            return redirect()->route('login')
                ->with('error', 'Too many login attempts. Try again in ' . RateLimiter::availableIn($key) . ' seconds.');
        }
        if ($this->attemptLogin($request)) {
            $user = Auth::user();
            RateLimiter::clear($key);
            return redirect()->intended(route('roles.index'));
        }

        Log::warning('Failed login attempt for email/username: ' . $request->email);
        RateLimiter::hit($key, 60);

        return redirect()->route('login')
            ->with('error', 'Invalid email/username or password.')
            ->withInput($request->only('email', 'remember'));
    }

    public function logout(Request $request)
    {
        Auth::logout();
        $request->session()->invalidate();
        $request->session()->regenerateToken();
        return redirect()->route('login');
    }

    protected function validateLogin(Request $request)
    {
        $request->validate([
            'email' => 'required|string',
            'password' => 'required|string',
        ], [
            'email.required' => 'Please enter your email or username.',
        ]);
    }

    protected function attemptLogin(Request $request)
    {
        return Auth::attempt([
            'email' => $request->email,
            'password' => $request->password,
        ], $request->filled('remember')) || Auth::attempt([
            'username' => $request->email,
            'password' => $request->password,
        ], $request->filled('remember'));
    }
}
