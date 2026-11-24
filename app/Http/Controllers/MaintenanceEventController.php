<?php

namespace App\Http\Controllers;

use App\Models\MaintenanceEvent;
use App\Models\MaintenanceSchedule;
use Illuminate\Http\Request;

class MaintenanceEventController extends Controller
{
    public function __construct()
    {
        $this->middleware('permission:maintenance.create');
    }

    public function create(Request $request)
    {
        $scheduleId = $request->query('maintenance_schedule_id');
        $maintenanceSchedule = $scheduleId
            ? MaintenanceSchedule::with(['vehicle', 'type'])->find($scheduleId)
            : null;

        return view('maintenance-events.create', compact('maintenanceSchedule'));
    }

    public function store(Request $request)
    {
        $data = $request->validate([
            'maintenance_schedule_id' => 'required|exists:maintenance_schedules,id',
            'work_details' => 'required|string',
            'cost' => 'required|numeric|min:0',
            'completed_at' => 'required|date',
            'status' => 'required|string|in:pending,completed,cancelled,in_progress',
        ]);

        MaintenanceEvent::create($data);

        return redirect('/maintenance-schedules')->with('success', 'Maintenance event recorded.');
    }
}