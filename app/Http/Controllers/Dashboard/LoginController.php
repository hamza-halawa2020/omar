<?php

namespace App\Http\Controllers\Dashboard;

use App\Http\Controllers\Controller;
use App\Http\Requests\Auth\LoginRequest;
use App\Http\Resources\UserResource;
use Illuminate\Support\Facades\Auth;
use Illuminate\Http\Request;



class LoginController extends Controller
{
    public function showLoginForm()
    {
        return view('dashboard.auth.login');
    }

    public function login(LoginRequest $request)
    {
        // dd($request->all());
        $email    = $request['email'];
        $password = $request['password'];
        $remember = $request['remember'];

        $attempts = [];

        if (filter_var($email, FILTER_VALIDATE_EMAIL)) {
            $attempts[] = ['email' => $email, 'password' => $password];
        }

        $authed = false;
        foreach ($attempts as $credentials) {
            if (Auth::attempt($credentials, $remember)) {
                $authed = true;
                break;
            }
        }

        if (! $authed) {
            if ($request->wantsJson()) {
                return response()->json(['status' => false, 'message' => __('messages.invalid_credentials')], 401);
            }
            return back()->withErrors(['login' => 'Invalid credentials.'])->withInput();
        }

        $request->session()->regenerate();
        $user = Auth::user();

        if ($request->wantsJson()) {
            return response()->json(['status'  => true, 'message' => __('messages.login_successful'), 'data'    => new UserResource($user),]);
        }

        return redirect()->intended('/dashboard');
    }

    public function logout(Request $request)
    {
        Auth::logout();
        $request->session()->invalidate();
        $request->session()->regenerateToken();

        if ($request->wantsJson()) {
            return response()->json(['status' => true, 'message' => __('messages.logged_out_successfully')]);
        }

        return redirect()->route('login');
    }
}
