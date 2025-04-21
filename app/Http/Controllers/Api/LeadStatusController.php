<?php

namespace App\Http\Controllers\Api;

use App\DTO\QueryFilters\LeadStatusFilter;
use App\Http\Controllers\Controller;
use App\Models\LeadsStatus;
use App\Services\LeadStatusServiceInterface;
use Illuminate\Http\Request;

class LeadStatusController extends Controller
{
    public function __construct(private LeadStatusServiceInterface $leadStatusService)
    {
    }

    public function list(Request $request)
    {
        $parentId = $request->query('parent_id');

        $defaultParentId = LeadsStatus::where('is_default', 1)->first()->id;

        return $this->leadStatusService->pluck('name', 'id', new LeadStatusFilter(parentId: $parentId ?? $defaultParentId));
    }
}
