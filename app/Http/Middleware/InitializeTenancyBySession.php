<?php

namespace App\Http\Middleware;

use App\Models\Tenant;
use Closure;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Spatie\Permission\PermissionRegistrar;

class InitializeTenancyBySession
{
    public function __construct(private PermissionRegistrar $permissionRegistrar) {}

    public function handle(Request $request, Closure $next)
    {
        $tenantId = session('tenant_id');

        if (! $tenantId) {
            $tenantId = Auth::guard('web')->user()?->tenant_id;

            if ($tenantId) {
                session(['tenant_id' => $tenantId]);
            }
        }

        if (! $tenantId) {
            if ($request->wantsJson()) {
                return response()->json(['status' => false, 'message' => __('messages.company_not_found')], 401);
            }

            return redirect()->route('login');
        }

        $tenant = Tenant::on('central')->find($tenantId);

        if (! $tenant) {
            Auth::guard('web')->logout();
            session()->forget('tenant_id');
            $request->session()->invalidate();
            $request->session()->regenerateToken();

            return redirect()->route('login')->withErrors(['login' => __('messages.company_not_found')]);
        }

        tenancy()->initialize($tenant);

        // Force reconnection so the current DB connection uses the initialized tenant database.
        $currentConnection = config('database.default');
        DB::purge($currentConnection);
        DB::reconnect($currentConnection);

        config([
            'permission.cache.key' => 'spatie.permission.cache.tenant.'.$tenant->getKey(),
        ]);

        $this->permissionRegistrar->initializeCache();
        $this->permissionRegistrar->clearPermissionsCollection();

        return $next($request);
    }
}
