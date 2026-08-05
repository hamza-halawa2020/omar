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
            'name'   => ['required', 'string', 'max:255'],
            'domain' => ['required', 'string', 'max:255', 'unique:central.tenants,domain'],
        ]);

        try {
            $this->service->createTenant($request->name, $request->domain);
            return redirect()->route('admin.dashboard')
                ->with('success', 'تم إنشاء الـ tenant بنجاح.');
        } catch (\Throwable $e) {
            return back()
                ->withInput()
                ->with('error', 'فشل إنشاء الـ tenant: ' . $e->getMessage());
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
                ->with('success', 'تم حذف الـ tenant بنجاح.');
        } catch (\Throwable $e) {
            return back()->with('error', 'فشل حذف الـ tenant: ' . $e->getMessage());
        }
    }
}
