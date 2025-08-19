<?php
namespace App\Support;

use Closure;

class TenancyScan
{
    /**
     * Run a callback for each tenant. Supports stancl/tenancy if installed.
     * Falls back to single-tenant (runs once) if no tenancy package is found.
     */
    public static function forEachTenant(Closure $callback): void
    {
        // stancl/tenancy
        if (class_exists(\Stancl\Tenancy\Tenant::class) && function_exists('tenancy')) {
            $tenants = \Stancl\Tenancy\Tenant::all();
            foreach ($tenants as $tenant) {
                tenancy()->initialize($tenant);
                try { $callback($tenant); } finally { tenancy()->end(); }
            }
            return;
        }

        // Custom App\Models\Tenant with a "run" method
        if (class_exists(\App\Models\Tenant::class)) {
            foreach (\App\Models\Tenant::all() as $tenant) {
                if (method_exists($tenant, 'run')) {
                    $tenant->run(fn() => $callback($tenant));
                } else {
                    $callback($tenant);
                }
            }
            return;
        }

        // No tenancy – run once
        $callback(null);
    }
}
