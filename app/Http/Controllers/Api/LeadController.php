<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Services\LeadServiceInterface;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Response;

class LeadController extends Controller
{
    public function __construct(private readonly LeadServiceInterface $leadService)
    {
    }

    public function list(): JsonResponse
    {
        return Response::json(
            $this->leadService->pluck('name', 'id')
        );
    }
}
