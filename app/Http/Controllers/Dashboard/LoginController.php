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
        if ($this->attemptLogin($request)) {
            $user = Auth::user();
            return redirect()->route('settings');
        }
        Log::warning('Failed login attempt for email/username: ' . $request->email);
        return redirect()->route('login')
            ->with('error', 'Invalid email/username or password.')
            ->withInput($request->only('email', 'remember'));
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
