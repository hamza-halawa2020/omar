<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Services\ContactServiceInterface;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Response;

class ContactController extends Controller
{
    public function __construct(private readonly ContactServiceInterface $contactService) {}

    public function list() : JsonResponse
    {
        return Response::json($this->contactService->pluck('name', 'id'));
    }
}
