<?php

namespace App\Http\Controllers\Dashboard;


use Illuminate\Routing\Controller as BaseController;

class DashboardController extends BaseController
{
    public function __construct()
    {
        // $this->middleware('check.permission:categories_index')->only('index');
        // $this->middleware('check.permission:categories_update')->only(['edit', 'update']);
    }



    public function index()
    {
        return view('dashboard.index');
    }
}
