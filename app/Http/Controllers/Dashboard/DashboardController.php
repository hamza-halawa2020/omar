<?php

namespace App\Http\Controllers\Dashboard;

use App\Http\Requests\Category\StoreCategoryRequest;
use App\Http\Requests\Category\UpdateCategoryRequest;
use App\Http\Resources\CategoryResource;
use App\Models\Category;
use Illuminate\Routing\Controller as BaseController;
use Illuminate\Support\Facades\Auth;

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
