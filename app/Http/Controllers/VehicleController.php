<?php

namespace App\Http\Controllers;

use App\Models\Vehicle;
use Illuminate\Http\Request;

class VehicleController extends Controller
{
    public function __construct()
    {
        $this->middleware('permission:vehicles.view')->only('index');
        $this->middleware('permission:vehicles.create')->only(['create', 'store']);
        $this->middleware('permission:vehicles.update')->only(['edit', 'update']);
        $this->middleware('permission:vehicles.delete')->only('destroy');
    }

    public function index()
    {
        $vehicles = Vehicle::all();
        return view('vehicles.index', compact('vehicles'));
    }

    public function create()
    {
        return view('vehicles.create');
    }

    public function store(Request $request)
    {
        $data = $request->validate([
            'vehicle_number' => 'required|string|max:255|unique:vehicles,vehicle_number',
            'make' => 'nullable|string|max:255',
            'model' => 'nullable|string|max:255',
            'year' => 'nullable|integer|min:1900|max:'.(date('Y') + 1),
            'vehicle_type' => 'nullable|string|max:255',
            'capacity' => 'nullable|string|max:255',
            'odometer_start' => 'nullable|integer|min:0',
            'status' => 'nullable|string|max:255',
        ]);

        Vehicle::create($data);

        return redirect('/vehicles')->with('success', 'Vehicle created.');
    }

    public function edit(Vehicle $vehicle)
    {
        return view('vehicles.edit', compact('vehicle'));
    }

    public function update(Request $request, Vehicle $vehicle)
    {
        $data = $request->validate([
            'vehicle_number' => 'required|string|max:255|unique:vehicles,vehicle_number,'.$vehicle->id,
            'make' => 'nullable|string|max:255',
            'model' => 'nullable|string|max:255',
            'year' => 'nullable|integer|min:1900|max:'.(date('Y') + 1),
            'vehicle_type' => 'nullable|string|max:255',
            'capacity' => 'nullable|string|max:255',
            'odometer_start' => 'nullable|integer|min:0',
            'status' => 'required|string|max:255',
        ]);
        $vehicle->update($data);

        return redirect('/vehicles')->with('success', 'Vehicle updated.');
    }

    public function destroy(Vehicle $vehicle)
    {
        if ($vehicle->trips()->exists() || $vehicle->assignments()->exists() || $vehicle->reports()->exists()) {
            return back()->withErrors([
                'vehicle' => 'Vehicles with assignments, trips, or condition reports cannot be deleted.',
            ]);
        }

        $vehicle->delete();

        return redirect('/vehicles')->with('success', 'Vehicle deleted.');
    }
}