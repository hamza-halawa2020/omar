<?php

namespace App\Http\Controllers\Dashboard;

use App\Enums\WorkFlow\WorkFlowType;
use App\Http\Controllers\Controller;
use App\Http\Requests\Dashboard\Crud\WorkFlow\StoreRequest;
use App\Models\WorkFlow;
use Illuminate\Http\Request;

class WorkFlowController extends Controller
{
    public function index(Request $request)
    {
        $workflows = Workflow::paginate(10);
        return view('dashboard.crud.workflow.index', compact('workflows'));
    }

    public function store(StoreRequest $request)
    {
        workFlow::create($request->validated());
        return redirect()->route('workflow.index')->with('success', 'Track type added successfully');
    }

    public function show(workFlow $workFlow)
    {
        $workFlows = WorkFlow::all();
        return view('dashboard.crud.workflow.edit', compact('workFlow', 'workFlows'));
    }


    public function edit($id)
    {
        $flow = WorkFlow::findOrFail($id);
        return view('dashboard.crud.workflow.edit', compact('flow'));
    }


    public function create()
    {
        $types = WorkFlowType::cases();
        return view('dashboard.crud.workflow.create', compact('types'));
    }


    public function update(StoreRequest $request, $id)
    {
        $workFlow = WorkFlow::findOrFail($id);
        $workFlow->update($request->validated());
        return redirect()->route('workflow.index')->with('success', 'Track type updated successfully');
    }

    public function destroy($id)
    {
        $workFlow = WorkFlow::findOrFail($id);
        $workFlow->delete();
        return redirect()->route('workflow.index')->with('success', 'Track type deleted successfully');
    }
}
