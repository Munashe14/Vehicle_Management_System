<?php

namespace Database\Seeders;

use App\Models\Permission;
use App\Models\Role;
use Illuminate\Database\Seeder;

class RbacSeeder extends Seeder
{
    public function run()
    {
        $permissions = [
            'vehicles.view' => 'View vehicles',
            'vehicles.create' => 'Create vehicles',
            'vehicles.update' => 'Update vehicles',
            'vehicles.delete' => 'Delete vehicles',
            'drivers.view' => 'View drivers',
            'drivers.create' => 'Create drivers',
            'drivers.update' => 'Update drivers',
            'drivers.delete' => 'Delete drivers',
            'profile.view' => 'View own profile',
            'allocations.view' => 'View driver allocations',
            'allocations.create' => 'Create driver allocations',
            'allocations.update' => 'Update driver allocations',
            'allocations.delete' => 'Delete driver allocations',
            'trips.view' => 'View trips',
            'trips.create' => 'Create trips',
            'trips.update' => 'Update trips',
            'trips.delete' => 'Delete trips',
            'fuel.view' => 'View fuel records',
            'fuel.create' => 'Create fuel records',
            'fuel.update' => 'Update fuel records',
            'maintenance.view' => 'View maintenance records',
            'maintenance.create' => 'Create maintenance records',
            'maintenance.update' => 'Update maintenance records',
            'maintenance.delete' => 'Delete maintenance records',
            'reports.fleet' => 'View fleet reports',
            'reports.fuel' => 'View fuel reports',
            'reports.maintenance' => 'View maintenance reports',
            'reports.trips' => 'View trip reports',
            'reports.financial' => 'View financial reports',
            'vehicle_reports.view' => 'View vehicle condition reports',
            'vehicle_reports.create' => 'Submit vehicle condition reports',
            'vehicle_reports.update' => 'Review vehicle condition reports',
            'users.view' => 'View user accounts',
            'users.create' => 'Create user accounts',
            'users.update' => 'Update user accounts and roles',
            'users.deactivate' => 'Deactivate user accounts',
            'audit.view' => 'View security audit logs',
        ];

        Permission::where('name', 'roles.manage')->delete();
        Permission::where('name', 'users.approve')->delete();

        foreach ($permissions as $name => $label) {
            Permission::updateOrCreate(['name' => $name], ['label' => $label]);
        }

        $all = array_keys($permissions);
        $fleetSupervisor = [
            'vehicles.view', 'vehicles.create', 'vehicles.update', 'vehicles.delete',
            'drivers.view', 'drivers.create', 'drivers.update', 'drivers.delete',
            'allocations.view', 'allocations.create', 'allocations.update', 'allocations.delete',
            'trips.view', 'trips.create', 'trips.update', 'trips.delete',
            'fuel.view', 'fuel.create', 'fuel.update',
            'maintenance.view', 'maintenance.create', 'maintenance.update', 'maintenance.delete',
            'vehicle_reports.view', 'vehicle_reports.create', 'vehicle_reports.update',
            'reports.fleet', 'reports.fuel', 'reports.maintenance', 'reports.trips',
        ];
        $driver = [
            'profile.view', 'trips.view', 'trips.update', 'fuel.view', 'fuel.create',
            'vehicle_reports.view', 'vehicle_reports.create',
        ];
        $financeManager = [
            'fuel.view', 'maintenance.view',
            'reports.fuel', 'reports.maintenance', 'reports.financial',
        ];
        $generalManager = [
            'vehicles.view', 'drivers.view', 'allocations.view', 'trips.view',
            'fuel.view', 'maintenance.view', 'vehicle_reports.view',
            'reports.fleet', 'reports.fuel', 'reports.maintenance', 'reports.trips', 'reports.financial',
        ];
        $rolePermissions = [
            'it_manager' => $all,
            'fleet_supervisor' => $fleetSupervisor,
            'driver' => $driver,
            'finance_manager' => $financeManager,
            'general_manager' => $generalManager,
        ];
        $labels = [
            'it_manager' => 'IT Manager / System Administrator',
            'fleet_supervisor' => 'Fleet Supervisor',
            'driver' => 'Drivers',
            'finance_manager' => 'Finance Manager',
            'general_manager' => 'General Manager',
        ];

        foreach ($rolePermissions as $name => $permissionNames) {
            $role = Role::updateOrCreate(['name' => $name], ['label' => $labels[$name]]);
            $role->permissions()->sync(
                Permission::whereIn('name', $permissionNames)->pluck('id')->all()
            );
        }
    }
}
