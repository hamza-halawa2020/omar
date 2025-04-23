<?php

namespace App\Http\Controllers\Dashboard;

use App\Http\Controllers\Controller;
use App\Http\Requests\Dashboard\Crud\ProgramType\StoreRequest;
use App\Models\ProgramType;
use Illuminate\Http\Request;

class ProgramTypeController extends Controller
{
    public function index(Request $request)
    {
        $types = ProgramType::paginate(10);
        return view('dashboard.crud.program_types.index', compact('types'));
    }

    public function store(StoreRequest $request)
    {
        ProgramType::create($request->validated());
        return redirect()->route('program_types.index')->with('success', 'Track type added successfully');
    }

    public function show($id)
    {
        $type = ProgramType::findOrFail($id);
        return view('dashboard.crud.program_types.edit', compact( 'type'));
    }


    public function edit($id)
    {
        $type = ProgramType::findOrFail($id);

        return view('dashboard.crud.program_types.edit', compact('type'));
    }


    public function create()
    {
        $types = ProgramType::all();
        return view('dashboard.crud.program_types.create', compact('types'));
    }


    public function update(StoreRequest $request, $id)
    {
        $type = ProgramType::findOrFail($id);
        $type->update($request->validated());
        return redirect()->route('program_types.index')->with('success', 'Track type updated successfully');
    }

    public function destroy($id)
    {
        $type = ProgramType::findOrFail($id);
        $type->delete();
        return redirect()->route('program_types.index')->with('success', 'Track type deleted successfully');
    }
}
