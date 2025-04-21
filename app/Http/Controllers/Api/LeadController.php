<?php

namespace App\Http\Controllers\Api;

use App\DTO\QueryFilters\LeadFilter;
use App\Http\Controllers\Controller;
use App\Http\Requests\Api\Leads\UpdateRequest;
use App\Models\Lead;
use App\Services\LeadServiceInterface;
use App\Services\LeadStatusServiceInterface;
use Illuminate\Http\JsonResponse;
use Illuminate\Support\Facades\Response;

class LeadController extends Controller
{
    public function __construct(
        private readonly LeadServiceInterface $leadService,
    )
    {
    }

    public function list(): JsonResponse
    {
        return Response::json(
            $this->leadService->pluck('first_name', 'id')
        );
    }

    public function update(UpdateRequest $request, Lead $lead): JsonResponse
    {
        $this->leadService->update($lead, $request->validated());

        return Response::json([
            'lead' => $this->leadService->getOne($lead->id)->load('status'),
        ]);
    }
}
