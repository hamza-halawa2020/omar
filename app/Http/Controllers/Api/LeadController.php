<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Http\Requests\Dashboard\Crud\Leads\UpdateRequest;
use App\Models\Lead;
use App\Models\LeadsStatus;
use App\Services\LeadServiceInterface;
use App\Services\LeadStatusServiceInterface;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Response;

class LeadController extends Controller
{
    public function __construct(
        private readonly LeadServiceInterface       $leadService,
        private readonly LeadStatusServiceInterface $leadStatusService,
    )
    {
    }

    public function list(): JsonResponse
    {
        return Response::json(
            $this->leadService->pluck('name', 'id')
        );
    }

    public function update(Request $request, Lead $lead)
    {
        $this->leadService->update($lead, [
            'status_id' => $request->status_id
        ]);

        return Response::json([
            'newStatus' => $this->leadStatusService->getById($request->status_id),
        ]);
    }
}
