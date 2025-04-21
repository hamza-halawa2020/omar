<?php

namespace App\Http\Controllers\Dashboard;

use App\DTO\QueryFilters\LeadFilter;
use App\Http\Controllers\Controller;
use App\Http\Requests\Dashboard\Crud\Leads\StoreRequest;
use App\Http\Requests\Dashboard\Crud\Leads\UpdateRequest;
use App\Models\Lead;
use App\Models\User;
use App\Services\LeadServiceInterface;
use Illuminate\Http\RedirectResponse;

class LeadController extends Controller
{
    public function __construct(private readonly LeadServiceInterface $leadService) {}

    public function index()
    {
        $leads = $this->leadService->getAll(new LeadFilter(withAssignedUsers: true, available: true, withStatus: true));

        return view('dashboard.crud.leads.index', [
            'leads' => $leads,
            'title' => 'Leads',
            'subTitle' => 'All leads'
        ]);
    }

    public function create()
    {
        $usersSelect = User::select(['id', 'full_name'])->get()->pluck('full_name', 'id');

        return view('dashboard.crud.leads.create', [
            'usersSelect' => $usersSelect,
            'title' => 'Leads',
            'subTitle' => 'Create Lead'
        ]);
    }

    public function store(StoreRequest $request): RedirectResponse
    {
        $this->leadService->create($request->validated());

        return redirect()->route('leads.index')->with('success', 'Lead created successfully.');
    }

    public function edit(Lead $lead)
    {
        $usersSelect = User::select(['id', 'full_name'])->get()->pluck('full_name', 'id');

        return view('dashboard.crud.leads.edit', [
            'lead' => $lead,
            'usersSelect' => $usersSelect,
            'title' => 'Leads',
            'subTitle' => 'Edit leads'
        ]);
    }

    public function update(UpdateRequest $request, Lead $lead): RedirectResponse
    {
        $this->leadService->update($lead, $request->validated());

        return redirect()->route('leads.index')->with('success', 'Lead updated successfully.');
    }

    public function destroy(Lead $lead): RedirectResponse
    {
        $this->leadService->delete($lead);

        return redirect()->route('leads.index')->with('success', 'Lead deleted successfully.');
    }
}
