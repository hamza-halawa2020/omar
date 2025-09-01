<?php

namespace App\Http\Controllers\Dashboard;

use App\Http\Controllers\Controller;
use App\Models\SmHumanDepartment;
use App\Models\Staff;
use App\Models\Tab;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class DepartmentController extends \Illuminate\Routing\Controller
{

    public function __construct()
    {
        $this->middleware('check.permission:general_departments_index')->only('index');
        $this->middleware('check.permission:general_departments_update')->only(['edit', 'update']);
    }

    public function index(Request $request)
    {
        $query = SmHumanDepartment::query();
        if ($search = $request->query('search')) {
            $query->where('name', 'like', "%{$search}%");
        }
        $sort = $request->query('sort', 'id');
        $order = $request->query('order', 'desc');
        $query->orderBy($sort, $order);
        $perPage = $request->query('per_page', 10);
        $departments = $query->latest()->paginate($perPage)->appends($request->query());
        return view('dashboard.departments.index', compact('departments'));
    }


    public function edit($id)
    {
        $tabs = Tab::with('children')->whereNull('parent_id')->get();
        $department = SmHumanDepartment::findOrFail($id);
        return view('dashboard.departments.edit', compact('department', 'tabs'));
    }

    public function update(Request $request, $id)
    {
        $department = SmHumanDepartment::findOrFail($id);
        $selectedTabs = $request->input('tabs', []);
        $department->tabs()->sync($selectedTabs);
        return redirect()->route('departments.index')->with('success', 'Department updated successfully with tabs.');
    }
}
