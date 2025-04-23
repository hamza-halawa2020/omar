<?php

namespace App\Http\Controllers\Dashboard;

use App\Http\Controllers\Controller;
use App\Http\Requests\Dashboard\Crud\Calls\StoreRequest;
use App\Models\Call;
use App\Models\WorkFlow;
use App\Services\CallServiceInterface;
use App\Services\ContactServiceInterface;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;

class CallController extends Controller
{
    public function __construct(
        private readonly CallServiceInterface    $callService,
        private readonly ContactServiceInterface $contactService,
    ) {}

    // public function index()
    // {
    //     $calls = $this->callService->getAll();

    //     return view('dashboard.crud.calls.index', [
    //         'calls' => $calls,
    //         'title' => 'Calls',
    //         'subTitle' => 'All Calls',
    //     ]);
    // }



    public function index()
    {
        $calls = $this->callService->getAll();
        $workflows = WorkFlow::with('callStatus')->where('type','call')->get();

        return view('dashboard.crud.calls.index', [
            'calls' => $calls,
            'workflows' => $workflows,
            'title' => 'Calls',
            'subTitle' => 'All Calls',
        ]);
    }

    public function kanbanPartial()
    {
        $calls = $this->callService->getAll();
        $workflows = WorkFlow::with('callStatus')->where('type','call')->get();


        return view('dashboard.crud.calls.partials.kanban', [
            'calls' => $calls,
            'workflows' => $workflows,
            'title' => 'Calls',
            'subTitle' => 'All Calls',
        ]);
    }

    public function listPartial()
    {
        $calls = $this->callService->getAll();
        $workflows = WorkFlow::with('callStatus')->where('type','call')->get();


        return view('dashboard.crud.calls.partials.list', [
            'calls' => $calls,
            'workflows' => $workflows,
            'title' => 'Calls',
            'subTitle' => 'All Calls',
        ]);
    }

    public function updateCallStatus(Request $request)
    {
        $request->validate([
            'id' => 'required|exists:crm_calls,id',
            'call_status_id' => 'required|exists:crm_call_statuses,id',
        ]);

        $call = Call::findOrFail($request->id);
        if ($call->call_status_id === $request->call_status_id) {
            Log::info('No change in call status, skipping update', ['call_id' => $call->id]);
            return response()->json(['message' => 'No change in call status']);
        }

        $call->call_status_id = $request->call_status_id;
        $call->save();

        return response()->json(['message' => 'Call status updated successfully']);
    }


    public function create()
    {
        $contacts = $this->contactService->pluck('first_name', 'id');

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
        $contacts = $this->contactService->pluck('first_name', 'id');

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
