<?php

namespace App\Http\Controllers\Dashboard;

use App\Http\Controllers\Controller;
use App\Models\SmHumanDepartment;
use App\Models\Staff;
use App\Models\Tab;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class TabController extends \Illuminate\Routing\Controller
{

    public function __construct()
    {
        $this->middleware('check.permission:general_tabs_index')->only('index');
        $this->middleware('check.permission:general_tabs_update')->only(['edit', 'update']);
    }

    public function index(Request $request)
    {
        $query = Tab::query();
        if ($search = $request->query('search')) {
            $query->where('label', 'like', "%{$search}%");
        }

        $perPage = $request->query('per_page', 10);

        $tabs = $query->latest()->paginate($perPage)->appends($request->query());



        return view('dashboard.tabs.index', compact('tabs'));
    }


    public function edit($id)
    {
        $tab = Tab::findOrFail($id);
        return view('dashboard.tabs.edit', compact('tab'));
    }

    public function update(Request $request, $id)
    {
        $tab = Tab::findOrFail($id);

        $validated = $request->validate([
            'label' => 'required|string|max:255',
            'icon' => 'nullable|string|max:255',
            'parent_id' => 'nullable|exists:tabs,id',
            'order' => 'nullable|integer',
        ]);

        $tab->update($validated);

        return redirect()->route('tabs.index')
            ->with('success', 'Tab updated successfully!');
    }
}
