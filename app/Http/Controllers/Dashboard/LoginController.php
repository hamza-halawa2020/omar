<?php

namespace App\Http\Controllers\Dashboard;

use App\Http\Controllers\Controller;
use App\Models\User;
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
            return redirect()->route('login')->with('error', 'Too many login attempts. Please try again in ' . $seconds . ' seconds.')->withInput($request->only('email', 'remember'));
        }

        if ($this->attemptLogin($request)) {
            $request->session()->regenerate();
            RateLimiter::clear($throttleKey);
            return redirect()->route('settings');
        }

        RateLimiter::hit($throttleKey, 300);
        return redirect()->route('login')->with('error', 'Invalid phone number, email, or password.')->withInput($request->only('email', 'remember'));
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
            'email.required' => 'Please enter your phone number or email.',
        ]);
    }




    protected function attemptLogin(Request $request)
    {
        $rawInput = $request->input('email');
        $input = preg_replace('/[^0-9+]/', '', $rawInput);
        $password = $request->input('password');
        $remember = $request->filled('remember');

        if (filter_var($rawInput, FILTER_VALIDATE_EMAIL)) {
            if (Auth::attempt(['email' => $rawInput, 'password' => $password], $remember)) {
                return true;
            }
        }

        $normalizedInput = ltrim($input, '+');

        $user = User::where('phone_number', $normalizedInput)->orWhereRaw("REPLACE(CONCAT(mobile_dial_code, phone_number), '+', '') = ?", [$normalizedInput])->first();

        if ($user) {
            return Auth::attempt([
                'phone_number' => $user->phone_number,
                'password' => $password
            ], $remember);
        }

        return false;
    }



    public function logout()
    {
        $userId = Auth::id();
        DB::table('sessions')->where('user_id', $userId)->delete();
        Auth::logout();
        return redirect()->route('login');
    }
}
