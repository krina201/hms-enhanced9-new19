<?php
namespace Database\Seeders;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Config;
use Spatie\Permission\Models\Permission;
use Spatie\Permission\Models\Role;
class HmsPermissionsSeeder extends Seeder {
    public function run(): void {
        $guard = Config::get('auth.defaults.guard','web');
        $perms = [
            'dashboard.view','activity_logs.view',
            'patients.view','patients.create','patients.edit',
            'visits.view','visits.create','visits.edit',
            'billing.view','billing.create','billing.edit',
            'purchase_orders.view','purchase_orders.create','purchase_orders.edit','purchase_orders.delete','purchase_orders.receive','purchase_orders.print',
            'grn.view','grn.export','labels.print',
            'reports.stock_aging.view','reports.stock_aging.export',
            'returns.view','returns.create',
            'requisitions.view','requisitions.edit','requisitions.submit','requisitions.approve','requisitions.convert',
        , 'patients.delete', 'patients.restore', 'patients.force_delete', 'visits.delete', 'visits.restore', 'visits.force_delete', 'billing.delete', 'billing.restore', 'billing.force_delete', 'admissions.delete', 'admissions.restore', 'admissions.force_delete'];
        foreach ($perms as $p) Permission::findOrCreate($p, $guard);
        $roles = [
            'Super Admin' => $perms,
            'Admin' => $perms,
            'Front Desk' => ['dashboard.view','patients.view','patients.create','patients.edit','visits.view','visits.create','visits.edit','billing.view'],
            'Doctor' => ['dashboard.view','patients.view','visits.view'],
            'Inventory Manager' => ['dashboard.view','purchase_orders.view','purchase_orders.receive','grn.view','labels.print','reports.stock_aging.view'],
        , 'patients.delete', 'patients.restore', 'patients.force_delete', 'visits.delete', 'visits.restore', 'visits.force_delete', 'billing.delete', 'billing.restore', 'billing.force_delete', 'admissions.delete', 'admissions.restore', 'admissions.force_delete'];
        foreach ($roles as $name=>$rperms) { $role = Role::findOrCreate($name, $guard); $role->syncPermissions($rperms); }
    }
}
