<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\VehicleController;
use App\Http\Controllers\DriverAssignmentController;
use App\Http\Controllers\TripController;
use App\Http\Controllers\MaintenanceTypeController;
use App\Http\Controllers\MaintenanceScheduleController;
use App\Http\Controllers\MaintenanceEventController;
use App\Http\Controllers\DriverController;
use App\Http\Controllers\AuthController;
use App\Http\Controllers\AdminUserController;
use App\Http\Controllers\AuditLogController;
use App\Http\Controllers\ProfileController;
use App\Http\Controllers\ReportController;
use App\Http\Controllers\VehicleReportController;

/*
|--------------------------------------------------------------------------
| Web Routes
|--------------------------------------------------------------------------
| These match the plain URL actions used inside the Blade views
| (the views were built without route names, using raw paths like
| "/vehicles", "/vehicles/{id}/edit", etc.). You still need real
| Controllers + Models + a migrated database for these to work —
| this file only wires the URLs to controller methods.
*/

Route::get('/login', [AuthController::class, 'showLogin'])->middleware('guest')->name('login');
Route::post('/login', [AuthController::class, 'login'])->middleware(['guest', 'throttle:5,1'])->name('login.submit');
Route::get('/register', [AuthController::class, 'showRegistration'])->middleware('guest')->name('register');
Route::post('/register', [AuthController::class, 'register'])->middleware(['guest', 'throttle:5,1'])->name('register.submit');

Route::middleware(['auth', 'active'])->group(function () {
    Route::post('/logout', [AuthController::class, 'logout'])->name('logout');
    Route::get('/profile', [ProfileController::class, 'show']);
    Route::get('/my-vehicle', [ProfileController::class, 'vehicle']);
    Route::get('/audit-logs', [AuditLogController::class, 'index']);
    Route::get('/reports/fleet', [ReportController::class, 'fleet']);
    Route::get('/reports/trips', [ReportController::class, 'trips']);
    Route::get('/reports/fuel', [ReportController::class, 'fuel']);
    Route::get('/reports/maintenance', [ReportController::class, 'maintenance']);
    Route::get('/reports/financial', [ReportController::class, 'financial']);
    Route::resource('vehicle-reports', VehicleReportController::class)
        ->only(['index', 'create', 'store', 'show', 'update']);

    Route::resource('users', AdminUserController::class)->except(['show', 'destroy']);
    Route::get('/users/{user}/password', [AdminUserController::class, 'editPassword']);
    Route::put('/users/{user}/password', [AdminUserController::class, 'updatePassword']);
    Route::patch('/users/{user}/deactivate', [AdminUserController::class, 'deactivate']);
    Route::patch('/users/{user}/approve', [AdminUserController::class, 'approve']);
    Route::patch('/users/{user}/reject', [AdminUserController::class, 'reject']);

    Route::resource('vehicles', VehicleController::class);

    Route::resource('driver-assignments', DriverAssignmentController::class)
        ->parameters(['driver-assignments' => 'assignment']);

    Route::resource('trips', TripController::class);
    Route::get('trips/{trip}/fuel', [TripController::class, 'editFuel']);
    Route::post('trips/{trip}/fuel', [TripController::class, 'storeFuel']);

    Route::resource('maintenance-types', MaintenanceTypeController::class)
        ->parameters(['maintenance-types' => 'type']);

    Route::resource('maintenance-schedules', MaintenanceScheduleController::class)
        ->parameters(['maintenance-schedules' => 'schedule']);

    Route::get('maintenance-events/create', [MaintenanceEventController::class, 'create']);
    Route::post('maintenance-events', [MaintenanceEventController::class, 'store']);

    Route::resource('drivers', DriverController::class)
        ->parameters(['drivers' => 'driver']);

    Route::get('/', function () {
        return app(\App\Http\Controllers\HomeController::class)->index();
    })->name('home');
});
