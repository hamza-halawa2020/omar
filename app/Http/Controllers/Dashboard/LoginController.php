<?php

namespace App\Http\Controllers\Dashboard;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\RateLimiter;
use Illuminate\Support\Facades\Log;
use Laravel\Sanctum\PersonalAccessToken;


class LoginController extends Controller
{

    public function showLoginForm()
    {
        return view('dashboard.auth.index');
    }
 

    public function login(Request $request)
    {
        $this->validateLogin($request);

        $throttleKey = $this->throttleKey($request);

        if (RateLimiter::tooManyAttempts($throttleKey, 5)) {
            $seconds = RateLimiter::availableIn($throttleKey);
            Log::warning('Too many login attempts for: ' . $request->email);
            return redirect()->route('login')
                ->with('error', 'Too many login attempts. Please try again in ' . $seconds . ' seconds.')
                ->withInput($request->only('email', 'remember'));
        }

        if ($this->attemptLogin($request)) {
            $request->session()->regenerate();
            RateLimiter::clear($throttleKey);
            $user = Auth::user();
            return redirect()->route('settings');
        }

        RateLimiter::hit($throttleKey, 300);
        Log::warning('Failed login attempt for email/username: ' . $request->email);
        return redirect()->route('login')
            ->with('error', 'Invalid email/username or password.')
            ->withInput($request->only('email', 'remember'));
    }

    protected function throttleKey(Request $request)
    {
        return strtolower($request->input('email')) . '|' . $request->ip();
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


    public function logout()
    {
        $userId = Auth::id();
        DB::table('sessions')->where('user_id', $userId)->delete();
        Auth::logout();
        return redirect()->route('login');
    }
}
