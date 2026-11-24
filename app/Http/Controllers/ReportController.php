<?php

namespace App\Http\Controllers;

use App\Models\Fuel;
use App\Models\MaintenanceEvent;
use App\Models\MaintenanceSchedule;
use App\Models\Trip;
use App\Models\Vehicle;
use Illuminate\Http\Request;

class ReportController extends Controller
{
    public function __construct()
    {
        $this->middleware('permission:reports.fleet')->only('fleet');
        $this->middleware('permission:reports.trips')->only('trips');
        $this->middleware('permission:reports.fuel')->only('fuel');
        $this->middleware('permission:reports.maintenance')->only('maintenance');
        $this->middleware('permission:reports.financial')->only('financial');
    }

    public function fleet(Request $request)
    {
        $rows = Vehicle::withCount('trips')->orderBy('vehicle_number')->get()
            ->map(function ($vehicle) {
                return [$vehicle->vehicle_number, $vehicle->make, $vehicle->model, $vehicle->status, $vehicle->trips_count];
            })->all();

        return $this->respond($request, 'Fleet overview', ['Vehicle', 'Make', 'Model', 'Status', 'Trips'], $rows);
    }

    public function trips(Request $request)
    {
        $rows = Trip::with(['vehicle', 'driver'])->latest('trip_date')->get()
            ->map(function ($trip) {
                return [$trip->id, $trip->vehicle->vehicle_number ?? '', $trip->driver->name ?? '', $trip->origin, $trip->destination, $trip->trip_date, $trip->status];
            })->all();

        return $this->respond($request, 'Trip report', ['Trip ID', 'Vehicle', 'Driver', 'Origin', 'Destination', 'Date', 'Status'], $rows);
    }

    public function fuel(Request $request)
    {
        $fuels = Fuel::with('trip.vehicle', 'trip.driver')->latest()->get();
        $rows = $fuels->map(function ($fuel) {
            return [
                $fuel->trip_id,
                $fuel->trip->vehicle->vehicle_number ?? '',
                $fuel->trip->driver->name ?? '',
                $fuel->fuel_type,
                $fuel->consumed_liters,
                $fuel->unit_cost,
                $fuel->total_cost,
            ];
        })->all();
        $total = $fuels->sum('total_cost');

        return $this->respond($request, 'Fuel report (total expenditure: '.$total.')', [
            'Trip ID', 'Vehicle', 'Driver', 'Fuel type', 'Liters', 'Unit cost', 'Total cost',
        ], $rows);
    }

    public function maintenance(Request $request)
    {
        $schedules = MaintenanceSchedule::with(['vehicle', 'type'])->orderBy('due_date')->get();
        $events = MaintenanceEvent::with('schedule.vehicle', 'schedule.type')->latest('completed_at')->get();
        $rows = [];
        foreach ($schedules as $schedule) {
            $rows[] = [
                'Schedule '.$schedule->id,
                $schedule->vehicle->vehicle_number ?? '',
                $schedule->type->name ?? '',
                'Due '.$schedule->due_date,
                '',
            ];
        }
        foreach ($events as $event) {
            $rows[] = [
                'Event '.$event->id,
                $event->schedule->vehicle->vehicle_number ?? '',
                $event->schedule->type->name ?? '',
                $event->status,
                $event->cost,
            ];
        }

        return $this->respond($request, 'Maintenance report (expenditure: '.$events->sum('cost').')', [
            'Record', 'Vehicle', 'Maintenance type', 'Due date / status', 'Cost',
        ], $rows);
    }

    public function financial(Request $request)
    {
        $fuelCost = Fuel::sum('total_cost');
        $maintenanceCost = MaintenanceEvent::sum('cost');

        return $this->respond($request, 'Fleet expenditure summary', ['Category', 'Total cost'], [
            ['Fuel', $fuelCost],
            ['Maintenance', $maintenanceCost],
            ['Total', $fuelCost + $maintenanceCost],
        ]);
    }

    private function respond(Request $request, string $title, array $headers, array $rows)
    {
        if ($request->query('format') === 'csv') {
            $filename = str_replace(' ', '-', strtolower($title)).'.csv';

            return response()->streamDownload(function () use ($headers, $rows) {
                $output = fopen('php://output', 'w');
                fputcsv($output, $headers);
                foreach ($rows as $row) {
                    foreach ($row as &$value) {
                        if (is_string($value) && preg_match('/^[=+\-@\t\r]/', $value)) {
                            $value = "'".$value;
                        }
                    }
                    unset($value);
                    fputcsv($output, $row);
                }
                fclose($output);
            }, $filename, ['Content-Type' => 'text/csv; charset=UTF-8']);
        }

        return view('reports.show', compact('title', 'headers', 'rows'));
    }
}
