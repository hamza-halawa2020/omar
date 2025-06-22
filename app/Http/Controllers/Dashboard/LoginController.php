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
            return redirect()->route('login')->with('error', 'Too many login attempts. Please try again in ' . $seconds . ' seconds.')->withInput($request->only('email', 'remember'));
        }

        if ($this->attemptLogin($request)) {
            $user = Auth::user();
            // Check for staff record
            $teacherRecord = DB::table('sm_staffs')->where('user_id', $user->id)->first();

            if ($teacherRecord) {
                $request->session()->regenerate();
                RateLimiter::clear($throttleKey);
                return redirect()->route('settings');
            }

            $request->session()->regenerate();
            RateLimiter::clear($throttleKey);
            return redirect()->intended(route('dashboard'));
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
        $input = $request->input('email');
        $password = $request->input('password');
        $remember = $request->filled('remember');

        // Step 2: Try login with email or username
        if (filter_var($input, FILTER_VALIDATE_EMAIL)) {
            $emailAttempt = Auth::attempt([
                'email' => $input,
                'password' => $password,
            ], $remember);

            if ($emailAttempt) {
                return true;
            }
        } else {
            $usernameAttempt = Auth::attempt([
                'username' => $input,
                'password' => $password,
            ], $remember);

            if ($usernameAttempt) {
                return true;
            }
        }

        // Step 3: Input is not an email or username, assume it's a phone number
        $cleanInput = preg_replace('/[^0-9+]/', '', $input);

        // Step 5: Get all possible dial codes from sm_staffs
        $dialCodes = DB::table('sm_staffs')->pluck('mobile_dial_code')->filter()->unique()->toArray();

        // Step 6: Normalize phone number for each dial code
        $normalizedInputs = [$cleanInput];
        foreach ($dialCodes as $dialCode) {
            $dialCodePattern = '/^(' . preg_quote($dialCode, '/') . '|'
                . preg_quote(ltrim($dialCode, '+'), '/') . ')([0-9]{9,})$/';
            if (preg_match($dialCodePattern, $cleanInput, $matches)) {
                $normalizedNumber = '0' . $matches[2];
                $normalizedInputs[] = $normalizedNumber;
                $normalizedInputs[] = $matches[2]; // Add number without leading 0 (e.g., 1236545000)
            }
        }

        // Step 7: Query sm_staffs for matching mobile or mobile_dial_code + mobile
        $staff = DB::table('sm_staffs')->where(function ($query) use ($normalizedInputs) {
            foreach ($normalizedInputs as $number) {
                $query->orWhere('mobile', $number)->orWhereRaw('CONCAT(mobile_dial_code, mobile) = ?', [$number]);
            }
        })->whereNotNull('user_id')->first();

        if ($staff) {
            $user = DB::table('users')->where('id', $staff->user_id)->first();

            if ($user) {
                $phoneAttempt = Auth::attempt([
                    'phone_number' => $user->phone_number,
                    'password' => $password,
                ], $remember);

                if ($phoneAttempt) {
                    return true;
                } else {
                    return false;
                }
            } else {
                return false;
            }
        } else {
            return false;
        }
    }

    public function logout()
    {
        $userId = Auth::id();
        DB::table('sessions')->where('user_id', $userId)->delete();
        Auth::logout();
        return redirect()->route('login');
    }
}