<?php

namespace App\Http\Controllers\Dashboard;

use App\Http\Controllers\Controller;
use App\Http\Requests\Auth\LoginRequest;
use App\Services\LoginService;
use Illuminate\Http\Request;

class LoginController extends Controller
{
    public function __construct(private readonly LoginService $loginService)
    {
    }

    public function showLoginForm()
    {
        return $this->loginService->showLoginForm();
    }

    public function login(LoginRequest $request)
    {
        return $this->loginService->login($request);
    }

    public function logout(Request $request)
    {
        return $this->loginService->logout($request);
    }
}
