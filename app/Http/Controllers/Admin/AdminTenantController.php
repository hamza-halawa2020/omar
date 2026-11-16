<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Tenant;
use App\Services\AdminTenantService;
use Illuminate\Http\Request;

class AdminTenantController extends Controller
{
    public function __construct(protected AdminTenantService $service) {}

    /**
     * List all tenants — Requirements: 2.1
     */
    public function index()
    {
        $tenants = Tenant::latest()->get();

        return view('admin.tenants.index', compact('tenants'));
    }

    /**
     * Show create form — Requirements: 3.1
     */
    public function create()
    {
        return view('admin.tenants.create');
    }

    /**
     * Store new tenant — Requirements: 3.1, 3.2, 3.3
     */
    public function store(Request $request)
    {
        $request->validate([
            'name' => ['required', 'string', 'max:255'],
            'domain' => ['required', 'string', 'max:255', 'unique:central.tenants,domain'],
            'company_type' => ['required', 'in:general,umrah'],
        ]);

        try {
            $this->service->createTenant($request->name, $request->domain, $request->company_type);

            return redirect()->route('admin.dashboard')
                ->with('success', __('umrah.admin_company_created'));
        } catch (\Throwable $e) {
            return back()
                ->withInput()
                ->with('error', __('umrah.admin_company_create_failed').$e->getMessage());
        }
    }

    /**
     * Delete tenant — Requirements: 7.1, 7.2, 7.3
     */
    public function destroy(Tenant $tenant)
    {
        try {
            $this->service->deleteTenant($tenant);

            return redirect()->route('admin.dashboard')
                ->with('success', __('umrah.admin_company_deleted'));
        } catch (\Throwable $e) {
            return back()->with('error', __('umrah.admin_company_delete_failed').$e->getMessage());
        }
    }
}
