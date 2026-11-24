<?php

namespace App\Http\Controllers;

use App\Models\DriverAssignment;
use App\Models\AuditLog;
use App\Models\Vehicle;
use App\Models\VehicleReport;
use Illuminate\Http\Request;

class VehicleReportController extends Controller
{
    public function __construct()
    {
        $this->middleware('permission:vehicle_reports.view')->only(['index', 'show']);
        $this->middleware('permission:vehicle_reports.create')->only(['create', 'store']);
        $this->middleware('permission:vehicle_reports.update')->only('update');
    }

    public function index()
    {
        $query = VehicleReport::with(['vehicle', 'reporter', 'reviewer'])->latest();
        if (auth()->user()->isRole('driver')) {
            $query->where('user_id', auth()->id());
        }

        $reports = $query->paginate(25);
        return view('vehicle-reports.index', compact('reports'));
    }

    public function create()
    {
        $vehicles = $this->availableVehiclesForCurrentUser();
        abort_if(auth()->user()->isRole('driver') && $vehicles->isEmpty(), 403, 'You need an active vehicle assignment to submit a report.');

        return view('vehicle-reports.create', compact('vehicles'));
    }

    public function store(Request $request)
    {
        $data = $request->validate([
            'vehicle_id' => 'required|exists:vehicles,id',
            'type' => 'required|in:condition,defect',
            'description' => 'required|string|min:5|max:5000',
        ]);

        if (auth()->user()->isRole('driver')) {
            $assigned = DriverAssignment::where('driver_user_id', auth()->id())
                ->where('vehicle_id', $data['vehicle_id'])
                ->whereNull('released_at')
                ->exists();
            if (!$assigned) {
                $this->recordDenied(Vehicle::class, (int) $data['vehicle_id'], 'vehicle_reports.assignment');
                abort(404);
            }
        }

        $report = VehicleReport::create([
            'user_id' => auth()->id(),
            'vehicle_id' => $data['vehicle_id'],
            'type' => $data['type'],
            'description' => $data['description'],
            'status' => 'open',
        ]);

        return redirect('/vehicle-reports/'.$report->id)->with('success', 'Vehicle report submitted.');
    }

    public function show(VehicleReport $vehicleReport)
    {
        $this->ensureReportAccess($vehicleReport);
        $vehicleReport->load(['vehicle', 'reporter', 'reviewer']);

        return view('vehicle-reports.show', compact('vehicleReport'));
    }

    public function update(Request $request, VehicleReport $vehicleReport)
    {
        $data = $request->validate([
            'status' => 'required|in:open,in_progress,resolved',
            'resolution' => 'nullable|string|max:5000',
        ]);

        $vehicleReport->update([
            'status' => $data['status'],
            'resolution' => $data['resolution'] ?? null,
            'reviewed_by' => auth()->id(),
        ]);

        return redirect('/vehicle-reports/'.$vehicleReport->id)->with('success', 'Report updated.');
    }

    private function ensureReportAccess(VehicleReport $report): void
    {
        if (auth()->user()->isRole('driver') && (int) $report->user_id !== (int) auth()->id()) {
            $this->recordDenied(VehicleReport::class, $report->id, 'vehicle_reports.own');
            abort(404);
        }
    }

    private function recordDenied(string $subjectType, int $subjectId, string $permission): void
    {
        AuditLog::create([
            'user_id' => auth()->id(),
            'action' => 'authorization.denied',
            'subject_type' => $subjectType,
            'subject_id' => $subjectId,
            'details' => ['permission' => $permission],
            'ip_address' => request()->ip(),
        ]);
    }

    private function availableVehiclesForCurrentUser()
    {
        if (!auth()->user()->isRole('driver')) {
            return Vehicle::orderBy('vehicle_number')->get();
        }

        return Vehicle::whereIn('id', DriverAssignment::where('driver_user_id', auth()->id())
            ->whereNull('released_at')
            ->select('vehicle_id'))
            ->orderBy('vehicle_number')->get();
    }
}
