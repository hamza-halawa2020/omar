<?php

namespace App\Http\Controllers\Dashboard;

use App\Http\Controllers\Controller;
use App\Http\Requests\Dashboard\Crud\Calls\StoreRequest;
use App\Models\Call;
use App\Services\CallServiceInterface;
use App\Services\ContactServiceInterface;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;

class CallController extends Controller
{
    public function __construct(
        private readonly CallServiceInterface    $callService,
        private readonly ContactServiceInterface $contactService,
    )
    {
    }

    public function index()
    {
        $calls = $this->callService->getAll();

        return view('dashboard.crud.calls.index', [
            'calls' => $calls,
            'title' => 'Calls',
            'subTitle' => 'All Calls',
        ]);
    }

    public function create()
    {
        $contacts = $this->contactService->pluck('name', 'id');

        return view('dashboard.crud.calls.create', [
            'contacts' => $contacts,
            'title' => 'Calls',
            'subTitle' => 'create Call',
        ]);
    }

    public function store(StoreRequest $request): RedirectResponse
    {
        $this->callService->create($request->validated());

        return redirect()->route('calls.index')->with('success', 'Call created successfully!');
    }

    public function edit(Call $call)
    {
        $contacts = $this->contactService->pluck('name', 'id');

        return view('dashboard.crud.calls.edit', [
            'call' => $call,
            'contacts' => $contacts,
            'title' => 'Calls',
            'subTitle' => 'Edit Call',
        ]);
    }

    public function update(StoreRequest $request, Call $call): RedirectResponse
    {
        $this->callService->update($call, $request->validated());

        return redirect()->route('calls.index')
            ->with('success', 'Call updated successfully!');
    }

    public function destroy(Call $call): RedirectResponse
    {
        $this->callService->delete($call);

        return redirect()->route('calls.index')
            ->with('success', 'Call deleted successfully!');
    }
}
