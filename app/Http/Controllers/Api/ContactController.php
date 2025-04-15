<?php

namespace App\Http\Controllers\Api;

use App\DTO\ContactFilter;
use App\Http\Controllers\Controller;
use App\Services\ContactServiceInterface;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Response;

class ContactController extends Controller
{
    public function __construct(private readonly ContactServiceInterface $contactService) {}

    public function list(Request $request)
    {
        $accountId = (int) request()->query('account_id');

        return Response::json($this->contactService->pluck('name', 'id', new ContactFilter(accountId: $accountId)));
    }
}
