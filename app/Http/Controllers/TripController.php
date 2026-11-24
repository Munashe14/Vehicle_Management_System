<?php

namespace App\Http\Controllers;

use App\Models\Trip;
use App\Models\Vehicle;
use App\Models\User;
use App\Models\Fuel;
use App\Models\AuditLog;
use Illuminate\Http\Request;

class TripController extends Controller
{
    public function __construct()
    {
        $this->middleware('permission:trips.view')->only('index');
        $this->middleware('permission:trips.create')->only(['create', 'store']);
        $this->middleware('permission:trips.update')->only(['edit', 'update']);
        $this->middleware('permission:trips.delete')->only('destroy');
        $this->middleware('permission:fuel.view')->only('editFuel');
        $this->middleware('permission:fuel.create')->only('storeFuel');
    }

    public function index()
    {
        $trips = Trip::with(['vehicle', 'driver'])
            ->when(auth()->user()->isRole('driver'), function ($query) {
                $query->where('driver_user_id', auth()->id());
            })->get();
        return view('trips.index', compact('trips'));
    }

    public function create()
    {
        $vehicles = Vehicle::all();
        $drivers = User::where('role_id', \App\Models\Role::where('name', 'driver')->value('id'))
            ->where('is_active', true)->orderBy('name')->get();
        return view('trips.create', compact('vehicles', 'drivers'));
    }

    public function store(Request $request)
    {
        $data = $request->validate([
            'vehicle_id' => 'required|exists:vehicles,id',
            'driver_user_id' => 'required|exists:users,id',
            'origin' => 'required|string',
            'destination' => 'required|string',
            'trip_date' => 'nullable|date',
        ]);

        $this->ensureDriverUser($data['driver_user_id']);
        Trip::create($data);

        return redirect('/trips')->with('success', 'Trip created.');
    }

    public function edit(Trip $trip)
    {
        $this->ensureTripAccess($trip);
        if (auth()->user()->isRole('driver') && $trip->status === 'completed') {
            abort(403, 'Completed trips cannot be edited.');
        }
        $vehicles = auth()->user()->isRole('driver') ? collect() : Vehicle::all();
        $drivers = auth()->user()->isRole('driver')
            ? collect()
            : User::where('role_id', \App\Models\Role::where('name', 'driver')->value('id'))
                ->where('is_active', true)->orderBy('name')->get();
        return view('trips.edit', compact('trip', 'vehicles', 'drivers'));
    }

    public function update(Request $request, Trip $trip)
    {
        $this->ensureTripAccess($trip);
        if (auth()->user()->isRole('driver') && $trip->status === 'completed') {
            abort(403, 'Completed trips cannot be edited.');
        }

        if (auth()->user()->isRole('driver')) {
            $data = $request->validate([
                'origin' => 'required|string|max:255',
                'destination' => 'required|string|max:255',
                'trip_date' => 'nullable|date',
                'status' => 'sometimes|in:in_progress,completed',
            ]);
            if (isset($data['status'])) {
                $allowedTransitions = $trip->status === 'assigned'
                    ? ['in_progress', 'completed']
                    : ($trip->status === 'in_progress' ? ['completed'] : []);
                if ($data['status'] !== $trip->status && !in_array($data['status'], $allowedTransitions, true)) {
                    abort(422, 'Invalid trip status transition.');
                }
            }
        } else {
            $data = $request->validate([
                'vehicle_id' => 'required|exists:vehicles,id',
                'driver_user_id' => 'required|exists:users,id',
                'origin' => 'required|string|max:255',
                'destination' => 'required|string|max:255',
                'trip_date' => 'nullable|date',
                'status' => 'sometimes|in:assigned,in_progress,completed',
            ]);
            $this->ensureDriverUser($data['driver_user_id']);
        }

        $trip->update($data);

        return redirect('/trips')->with('success', 'Trip updated.');
    }

    public function destroy(Trip $trip)
    {
        if ($trip->fuel()->exists()) {
            return back()->withErrors([
                'trip' => 'Trips with fuel records cannot be deleted.',
            ]);
        }

        $trip->delete();

        return redirect('/trips')->with('success', 'Trip deleted.');
    }

    public function editFuel(Trip $trip)
    {
        $this->ensureTripAccess($trip);
        $fuel = Fuel::where('trip_id', $trip->id)->first();
        return view('trips.fuel', compact('trip', 'fuel'));
    }

    public function storeFuel(Request $request, Trip $trip)
    {
        $this->ensureTripAccess($trip);
        $existing = Fuel::where('trip_id', $trip->id)->first();
        if ($existing && !auth()->user()->hasPermission('fuel.update')) {
            abort(403, 'You are not authorized to update this fuel record.');
        }

        $data = $request->validate([
            'fuel_type' => 'required|string|max:255',
            'consumed_liters' => 'required|numeric|min:0',
            'unit_cost' => 'required|numeric|min:0',
        ]);

        Fuel::updateOrCreate(
            ['trip_id' => $trip->id],
            [
                'fuel_type' => $data['fuel_type'],
                'consumed_liters' => $data['consumed_liters'],
                'unit_cost' => $data['unit_cost'],
                'total_cost' => $data['consumed_liters'] * $data['unit_cost'],
            ]
        );

        return redirect('/trips')->with('success', 'Fuel record saved.');
    }

    private function ensureTripAccess(Trip $trip): void
    {
        if (auth()->user()->isRole('driver') && (int) $trip->driver_user_id !== (int) auth()->id()) {
            AuditLog::create([
                'user_id' => auth()->id(),
                'action' => 'authorization.denied',
                'subject_type' => Trip::class,
                'subject_id' => $trip->id,
                'details' => ['permission' => 'trips.own'],
                'ip_address' => request()->ip(),
            ]);
            abort(404);
        }
    }

    private function ensureDriverUser($id): void
    {
        $driverRoleId = \App\Models\Role::where('name', 'driver')->value('id');
        abort_unless(User::whereKey($id)->where('role_id', $driverRoleId)->where('is_active', true)->exists(), 422);
    }
}