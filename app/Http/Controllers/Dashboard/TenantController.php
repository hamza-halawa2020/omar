<?php

namespace App\Http\Controllers\Dashboard;

use App\Http\Controllers\Controller;
use App\Services\TenantService;
use Illuminate\Http\Request;

class TenantController extends Controller
{
    public function __construct(private readonly TenantService $tenantService) {}

    public function index()
    {
        $tenants = $this->tenantService->list();
        return view('dashboard.tenants.index', compact('tenants'));
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'name'   => 'required|string|max:255',
            'domain' => 'required|string|unique:tenants,domain',
        ]);

        $this->tenantService->create($validated);

        return response()->json(['status' => true, 'message' => 'Tenant created successfully.'], 201);
    }

    public function destroy(string $id)
    {
        $this->tenantService->delete($id);

        return response()->json(['status' => true, 'message' => 'Tenant deleted successfully.']);
    }
}
