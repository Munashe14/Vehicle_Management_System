<?php

namespace App\Providers;

use App\Models\DriverAssignment;
use App\Models\Fuel;
use App\Models\MaintenanceEvent;
use App\Models\MaintenanceSchedule;
use App\Models\MaintenanceType;
use App\Models\Trip;
use App\Models\Vehicle;
use App\Models\VehicleReport;
use App\Observers\OperationalAuditObserver;
use Illuminate\Support\ServiceProvider;

class AppServiceProvider extends ServiceProvider
{
    /**
     * Register any application services.
     *
     * @return void
     */
    public function register()
    {
        //
    }

    /**
     * Bootstrap any application services.
     *
     * @return void
     */
    public function boot()
    {
        foreach ([
            Vehicle::class,
            DriverAssignment::class,
            Trip::class,
            Fuel::class,
            MaintenanceType::class,
            MaintenanceSchedule::class,
            MaintenanceEvent::class,
            VehicleReport::class,
        ] as $model) {
            $model::observe(OperationalAuditObserver::class);
        }
    }
}
