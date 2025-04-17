<?php

namespace App\Http\Controllers\Dashboard;

use App\Http\Controllers\Controller;
use App\Models\Lead;
use App\Services\LeadServiceInterface;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;

class ConvertLeadController extends Controller
{
    public function __construct(private readonly LeadServiceInterface $leadService)
    {
    }

    public function __invoke(Lead $lead): RedirectResponse
    {
        $this->leadService->convertIntoAccountAndContact($lead);

        return redirect()->route('leads.index');
    }
}
