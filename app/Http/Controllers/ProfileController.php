<?php

namespace App\Http\Controllers;

use App\Models\DriverAssignment;

class ProfileController extends Controller
{
    public function __construct()
    {
        $this->middleware('permission:profile.view');
    }

    public function show()
    {
        return view('profile.show', ['user' => auth()->user()]);
    }

    public function vehicle()
    {
        abort_unless(auth()->user()->isRole('driver'), 404);
        $assignment = DriverAssignment::with('vehicle')
            ->where('driver_user_id', auth()->id())
            ->whereNull('released_at')
            ->latest('assigned_at')
            ->first();

        return view('profile.vehicle', compact('assignment'));
    }
}
