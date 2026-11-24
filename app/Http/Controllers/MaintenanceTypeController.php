<?php

namespace App\Http\Controllers;

use App\Models\MaintenanceType;
use Illuminate\Http\Request;

class MaintenanceTypeController extends Controller
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
        $types = MaintenanceType::all();
        return view('maintenance-types.index', compact('types'));
    }

    public function create()
    {
        return view('maintenance-types.create');
    }

    public function store(Request $request)
    {
        $request->validate(['name' => 'required|string']);

        MaintenanceType::create($request->validate(['name' => 'required|string|max:255']));

        return redirect('/maintenance-types')->with('success', 'Type created.');
    }

    public function edit(MaintenanceType $type)
    {
        return view('maintenance-types.edit', compact('type'));
    }

    public function update(Request $request, MaintenanceType $type)
    {
        $type->update($request->validate(['name' => 'required|string|max:255']));

        return redirect('/maintenance-types')->with('success', 'Type updated.');
    }

    public function destroy(MaintenanceType $type)
    {
        if ($type->schedules()->exists()) {
            return back()->withErrors([
                'type' => 'Maintenance types with schedules cannot be deleted.',
            ]);
        }

        $type->delete();

        return redirect('/maintenance-types')->with('success', 'Type deleted.');
    }
}