<?php

namespace App\Http\Controllers;

use App\Models\User;
use App\Models\Role;
use App\Models\AuditLog;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Str;

class DriverController extends Controller
{
    public function __construct()
    {
        $this->middleware('permission:drivers.view')->only('index');
        $this->middleware('permission:drivers.create')->only(['create', 'store']);
        $this->middleware('permission:drivers.update')->only(['edit', 'update']);
        $this->middleware('permission:drivers.delete')->only('destroy');
    }

    public function index()
    {
        $driverRoleId = Role::where('name', 'driver')->value('id');
        $drivers = User::where('role_id', $driverRoleId)->orderBy('name')->get();
        return view('drivers.index', compact('drivers'));
    }

    public function create()
    {
        return view('drivers.create');
    }

    public function store(Request $request)
    {
        $data = $request->validate([
            'name' => 'required|string|max:255',
            'email' => 'required|email|max:255|unique:users,email',
            'phone' => 'nullable|string|max:255',
            'license_number' => 'nullable|string|max:255|unique:users,license_number',
        ]);

        $driverRole = Role::where('name', 'driver')->firstOrFail();
        $driver = User::create([
            'name' => $data['name'],
            'email' => $data['email'],
            'phone' => $data['phone'] ?? null,
            'license_number' => $data['license_number'] ?? null,
            'password' => Hash::make(Str::random(48)),
            'role_id' => $driverRole->id,
            'is_active' => true,
        ]);
        AuditLog::create([
            'user_id' => auth()->id(),
            'action' => 'driver.created',
            'subject_type' => User::class,
            'subject_id' => $driver->id,
            'details' => ['role' => 'driver'],
            'ip_address' => $request->ip(),
        ]);

        return redirect('/drivers')->with('success', 'Driver added.');
    }

    public function edit(User $driver)
    {
        $this->ensureDriver($driver);
        return view('drivers.edit', compact('driver'));
    }

    public function update(Request $request, User $driver)
    {
        $this->ensureDriver($driver);
        $data = $request->validate([
            'name' => 'required|string|max:255',
            'email' => 'required|email|max:255|unique:users,email,'.$driver->id,
            'phone' => 'nullable|string|max:255',
            'license_number' => 'nullable|string|max:255|unique:users,license_number,'.$driver->id,
        ]);

        $driver->update($data);
        AuditLog::create([
            'user_id' => auth()->id(),
            'action' => 'driver.updated',
            'subject_type' => User::class,
            'subject_id' => $driver->id,
            'details' => ['fields' => array_keys($data)],
            'ip_address' => $request->ip(),
        ]);

        return redirect('/drivers')->with('success', 'Driver updated.');
    }

    public function destroy(User $driver)
    {
        $this->ensureDriver($driver);
        $driver->is_active = false;
        $driver->save();
        AuditLog::create([
            'user_id' => auth()->id(),
            'action' => 'driver.deactivated',
            'subject_type' => User::class,
            'subject_id' => $driver->id,
            'details' => [],
            'ip_address' => request()->ip(),
        ]);

        return redirect('/drivers')->with('success', 'Driver deactivated.');
    }

    private function ensureDriver(User $driver): void
    {
        abort_unless($driver->isRole('driver'), 404);
    }
}