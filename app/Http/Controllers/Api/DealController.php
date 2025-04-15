<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Services\DealServiceInterface;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Response;

class DealController extends Controller
{
    public function __construct(private readonly DealServiceInterface $dealService)
    {
    }

    public function list(): JsonResponse
    {
        return Response::json(
            $this->dealService->pluck('title', 'id')
        );
    }
}
