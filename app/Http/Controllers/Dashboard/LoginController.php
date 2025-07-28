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

        $result = $this->attemptLogin($request);

        if ($result['success']) {
            $request->session()->regenerate();
            RateLimiter::clear($throttleKey);
            return redirect()->route('settings');
        }

        RateLimiter::hit($throttleKey, 300);
        return redirect()->route('login')->with('error', $result['message'])->withInput($request->only('email', 'remember'));
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

        $user = User::with(['staff.department'])->where(function ($q) use ($rawInput, $input) {
            $q->where('email', $rawInput)
                ->orWhere('phone_number', ltrim($input, '+'))
                ->orWhereRaw("REPLACE(CONCAT(mobile_dial_code, phone_number), '+', '') = ?", [ltrim($input, '+')]);
        })->first();

        if (!$user) {
            return ['success' => false, 'message' => 'No user found with provided credentials.'];
        }

        $staff = $user->staff;

        if (!$staff || $staff->active_status != 1) {
            return ['success' => false, 'message' => 'Your account is inactive. Please contact support.'];
        }

        $department = $staff?->department;

        if ($department && in_array($department->slug, ['tester', 'teacher'])) {
            return ['success' => false, 'message' => 'You are not allowed to login from this portal.'];
        }

        if (Auth::attempt(['email' => $user->email, 'password' => $password], $remember)) {
            return ['success' => true, 'message' => 'Login successful.'];
        } else {
            return ['success' => false, 'message' => 'Invalid password.'];
        }
    }


    public function fcmToken(Request $request)
    {
        // $request->validate([
        //     'token' => 'required|string|unique:users,fcm_token',
        //     'device' => 'nullable|string'
        // ]);

        $request->user()->update([
            'fcm_token' => $request->token,
        ]);

        return response()->json(['message' => 'FCM Token saved']);
    }


    public function logout()
    {
        $userId = Auth::id();
        DB::table('sessions')->where('user_id', $userId)->delete();
        Auth::logout();
        return redirect()->route('login');
    }
}
