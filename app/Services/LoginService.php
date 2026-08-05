<?php

namespace App\Services;

use App\Events\CreateBackup;
use App\Http\Resources\UserResource;
use App\Models\Tenant;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Str;

class LoginService
{
    public function showLoginForm()
    {
        if (auth()->check()) {
            return redirect()->route('dashboard.index');
        }

        return view('dashboard.auth.login');
    }

    public function login(Request $request)
    {
        $email = $request['email'];
        $password = $request['password'];
        $remember = $request['remember'];

        // Extract domain from email and find tenant
        $domain = Str::after($email, '@');
        $tenant = Tenant::where('domain', $domain)->first();

        if (! $tenant) {
            if ($request->wantsJson()) {
                return response()->json(['status' => false, 'message' => __('messages.invalid_credentials')], 401);
            }
            return back()->withErrors(['login' => 'Invalid credentials.'])->withInput();
        }

        if (! Auth::attempt(['email' => $email, 'password' => $password], $remember)) {
            if ($request->wantsJson()) {
                return response()->json(['status' => false, 'message' => __('messages.invalid_credentials')], 401);
            }
            return back()->withErrors(['login' => 'Invalid credentials.'])->withInput();
        }

        // Make sure user belongs to this tenant
        if (Auth::user()->tenant_id !== $tenant->id) {
            Auth::logout();
            if ($request->wantsJson()) {
                return response()->json(['status' => false, 'message' => __('messages.invalid_credentials')], 401);
            }
            return back()->withErrors(['login' => 'Invalid credentials.'])->withInput();
        }

        $request->session()->regenerate();
        session(['tenant_id' => $tenant->id]);

        tenancy()->initialize($tenant);

        $user = Auth::user();

        if ($request->wantsJson()) {
            return response()->json([
                'status' => true,
                'message' => __('messages.login_successful'),
                'data' => new UserResource($user),
            ]);
        }

        event(new CreateBackup);

        return redirect()->intended('/dashboard');
    }

    public function logout(Request $request)
    {
        if (tenancy()->initialized) {
            tenancy()->end();
        }

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
