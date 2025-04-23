<?php

namespace App\Http\Controllers\Dashboard;

use App\Http\Controllers\Controller;
use App\Http\Requests\Dashboard\Crud\CallStatus\StoreRequest;
use App\Models\CallStatus;
use App\Models\WorkFlow;
use Illuminate\Http\Request;

class CallStatusController extends Controller
{
    public function index(Request $request)
    {
        $calls = CallStatus::with('workFlow')->paginate(10);
        return view('dashboard.crud.call_status.index', compact('calls'));
    }

    public function store(StoreRequest $request)
    {
        CallStatus::create($request->validated());
        return redirect()->route('call_status.index')->with('success', 'Track type added successfully');
    }

    public function show($id)
    {
        $call = CallStatus::findOrFail($id);
        return view('dashboard.crud.call_status.edit', compact( 'call'));
    }


    public function edit($id)
    {
        $call = CallStatus::with('workFlow')->findOrFail($id);
        $types = WorkFlow::all();

        return view('dashboard.crud.call_status.edit', compact('call','types'));
    }


    public function create()
    {
        $types = WorkFlow::all();
        return view('dashboard.crud.call_status.create', compact('types'));
    }


    public function update(StoreRequest $request, $id)
    {
        $workFlow = CallStatus::findOrFail($id);
        $workFlow->update($request->validated());
        return redirect()->route('call_status.index')->with('success', 'Track type updated successfully');
    }

    public function destroy($id)
    {
        $workFlow = CallStatus::findOrFail($id);
        $workFlow->delete();
        return redirect()->route('call_status.index')->with('success', 'Track type deleted successfully');
    }
}
