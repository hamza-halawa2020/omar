<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use App\Models\Tenant;
use Spatie\Permission\PermissionRegistrar;

class InitializeTenancyBySession
{
    public function __construct(private PermissionRegistrar $permissionRegistrar) {}

    public function handle(Request $request, Closure $next)
    {
        $tenantId = session('tenant_id');

        if (! $tenantId) {
            if ($request->wantsJson()) {
                return response()->json(['status' => false, 'message' => 'Tenant not found.'], 401);
            }
            return redirect()->route('login');
        }

        $tenant = Tenant::find($tenantId);

        if (! $tenant) {
            session()->forget('tenant_id');
            return redirect()->route('login')->withErrors(['login' => 'Tenant not found.']);
        }

        tenancy()->initialize($tenant);

        // Force reconnection so the current DB connection uses the initialized tenant database.
        $currentConnection = config('database.default');
        DB::purge($currentConnection);
        DB::reconnect($currentConnection);

        // Clear Spatie permission cache so it reads fresh from tenant DB.
        $this->permissionRegistrar->forgetCachedPermissions();

        return $next($request);
    }
}
