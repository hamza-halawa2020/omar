<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Tenant;

class AdminDashboardController extends Controller
{
    /**
     * Display all tenants in the admin dashboard — Requirements: 2.1
     */
    public function index()
    {
        $tenants = Tenant::latest()->get();
        return view('admin.dashboard', compact('tenants'));
    }
}
