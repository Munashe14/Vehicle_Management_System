<?php

namespace App\Http\Controllers;

use App\Models\DriverAssignment;
use App\Models\Vehicle;
use App\Models\User;
use App\Models\Role;
use Illuminate\Http\Request;

class DriverAssignmentController extends Controller
{
    public function __construct()
    {
        $this->middleware('permission:allocations.view')->only('index');
        $this->middleware('permission:allocations.create')->only(['create', 'store']);
        $this->middleware('permission:allocations.update')->only(['edit', 'update']);
        $this->middleware('permission:allocations.delete')->only('destroy');
    }

    public function index()
    {
        $assignments = DriverAssignment::with(['vehicle', 'driver'])->get();
        return view('driver-assignments.index', compact('assignments'));
    }

    public function create()
    {
        $vehicles = Vehicle::all();
        $drivers = $this->drivers();
        return view('driver-assignments.create', compact('vehicles', 'drivers'));
    }

    public function store(Request $request)
    {
        $data = $request->validate([
            'vehicle_id' => 'required|exists:vehicles,id',
            'driver_user_id' => 'required|exists:users,id',
            'assigned_at' => 'required|date',
            'released_at' => 'nullable|date|after_or_equal:assigned_at',
        ]);

        $this->ensureDriverId($data['driver_user_id']);
        DriverAssignment::create($data);

        return redirect('/driver-assignments')->with('success', 'Assignment created.');
    }

    public function edit(DriverAssignment $assignment)
    {
        $vehicles = Vehicle::all();
        $drivers = $this->drivers();
        return view('driver-assignments.edit', compact('assignment', 'vehicles', 'drivers'));
    }

    public function update(Request $request, DriverAssignment $assignment)
    {
        $data = $request->validate([
            'vehicle_id' => 'required|exists:vehicles,id',
            'driver_user_id' => 'required|exists:users,id',
            'assigned_at' => 'required|date',
            'released_at' => 'nullable|date|after_or_equal:assigned_at',
        ]);
        $this->ensureDriverId($data['driver_user_id']);
        $assignment->update($data);

        return redirect('/driver-assignments')->with('success', 'Assignment updated.');
    }

    public function destroy(DriverAssignment $assignment)
    {
        $assignment->delete();

        return redirect('/driver-assignments')->with('success', 'Assignment deleted.');
    }

    private function drivers()
    {
        return User::where('role_id', Role::where('name', 'driver')->value('id'))
            ->where('is_active', true)->orderBy('name')->get();
    }

    private function ensureDriverId($id): void
    {
        $driverRoleId = Role::where('name', 'driver')->value('id');
        abort_unless(User::whereKey($id)->where('role_id', $driverRoleId)->where('is_active', true)->exists(), 422);
    }
}