<?php

namespace App\Http\Controllers\Dashboard;

use App\Services\DashboardService;
use Illuminate\Http\Request;
use Illuminate\Routing\Controller as BaseController;

class DashboardController extends BaseController
{
    public function __construct(private readonly DashboardService $dashboardService)
    {
        // $this->middleware('check.permission:dashboard_index')->only('index');
    }

    public function index()
    {
        return view('dashboard.index');
    }

    public function analytics(Request $request)
    {
        return response()->json($this->dashboardService->analytics($request));
    }
}
