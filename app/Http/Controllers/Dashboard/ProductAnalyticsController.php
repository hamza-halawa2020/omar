<?php

namespace App\Http\Controllers\Dashboard;

use App\Services\ProductAnalyticsService;
use Illuminate\Http\Request;
use Illuminate\Routing\Controller as BaseController;

class ProductAnalyticsController extends BaseController
{
    public function __construct(private readonly ProductAnalyticsService $productAnalyticsService)
    {
        $this->middleware('check.permission:products_index')->only('index');
    }

    public function index(Request $request)
    {
        return view('dashboard.products.analytics', $this->productAnalyticsService->analytics($request));
    }
}
