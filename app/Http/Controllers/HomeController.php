<?php

namespace App\Http\Controllers;

class HomeController extends Controller
{
    public function index()
    {
        $user = auth()->user();
        $roleLandingPages = [
            'it_manager' => ['users.view', '/users'],
            'fleet_supervisor' => ['vehicles.view', '/vehicles'],
            'driver' => ['trips.view', '/trips'],
            'finance_manager' => ['reports.financial', '/reports/financial'],
            'general_manager' => ['reports.fleet', '/reports/fleet'],
        ];

        $landingPage = $roleLandingPages[$user->role->name] ?? null;
        if ($landingPage && $user->hasPermission($landingPage[0])) {
            return redirect($landingPage[1]);
        }

        abort(403, 'No landing page is available for this account.');
    }
}
