<?php

namespace App\Http\Controllers\Dashboard;

use App\Events\CreateBackup;
use App\Http\Controllers\Controller;
use App\Http\Requests\Auth\LoginRequest;
use App\Http\Resources\UserResource;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class LoginController extends Controller
{
    public function showLoginForm()
    {
        return view('dashboard.auth.login');
    }

    public function login(LoginRequest $request)
    {
        // dd($request->all());
        $email = $request['email'];
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
            return response()->json(['status' => true, 'message' => __('messages.login_successful'), 'data' => new UserResource($user)]);
        }

        event(new CreateBackup);

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

        event(new CreateBackup);

        return redirect()->route('login');
    }
}
