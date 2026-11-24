<?php

namespace App\Http\Controllers;

use App\Models\MaintenanceSchedule;
use App\Models\Vehicle;
use App\Models\MaintenanceType;
use Illuminate\Http\Request;

class MaintenanceScheduleController extends Controller
{
    public function __construct()
    {
        $this->middleware('permission:maintenance.view')->only('index');
        $this->middleware('permission:maintenance.create')->only(['create', 'store']);
        $this->middleware('permission:maintenance.update')->only(['edit', 'update']);
        $this->middleware('permission:maintenance.delete')->only('destroy');
    }

    public function index()
    {
        $schedules = MaintenanceSchedule::with(['vehicle', 'type'])->get();
        return view('maintenance-schedules.index', compact('schedules'));
    }

    public function create()
    {
        $vehicles = Vehicle::all();
        $types = MaintenanceType::all();
        return view('maintenance-schedules.create', compact('vehicles', 'types'));
    }

    public function store(Request $request)
    {
        $request->validate([
            'vehicle_id' => 'required|exists:vehicles,id',
            'maintenance_type_id' => 'required|exists:maintenance_types,id',
            'interval_value' => 'required|integer',
            'due_date' => 'required|date',
        ]);

        MaintenanceSchedule::create($request->validate([
            'vehicle_id' => 'required|exists:vehicles,id',
            'maintenance_type_id' => 'required|exists:maintenance_types,id',
            'interval_value' => 'required|integer|min:1',
            'due_date' => 'required|date',
        ]));

        return redirect('/maintenance-schedules')->with('success', 'Schedule created.');
    }

    public function edit(MaintenanceSchedule $schedule)
    {
        $vehicles = Vehicle::all();
        $types = MaintenanceType::all();
        return view('maintenance-schedules.edit', compact('schedule', 'vehicles', 'types'));
    }

    public function update(Request $request, MaintenanceSchedule $schedule)
    {
        $schedule->update($request->validate([
            'vehicle_id' => 'required|exists:vehicles,id',
            'maintenance_type_id' => 'required|exists:maintenance_types,id',
            'interval_value' => 'required|integer|min:1',
            'due_date' => 'required|date',
        ]));

        return redirect('/maintenance-schedules')->with('success', 'Schedule updated.');
    }

    public function destroy(MaintenanceSchedule $schedule)
    {
        if ($schedule->events()->exists()) {
            return back()->withErrors([
                'schedule' => 'Schedules with maintenance events cannot be deleted.',
            ]);
        }

        $schedule->delete();

        return redirect('/maintenance-schedules')->with('success', 'Schedule deleted.');
    }
}