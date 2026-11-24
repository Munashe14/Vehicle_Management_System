<x-app-layout title="Vehicle Reports · Fleet Manager">
    <div class="page-header">
        <div><span class="page-eyebrow">Vehicle condition</span><h1 class="page-title">Vehicle reports</h1></div>
        @if (auth()->user()->hasPermission('vehicle_reports.create'))<a class="btn btn-primary" href="/vehicle-reports/create">Submit report</a>@endif
    </div>
    @if (session('success'))<div class="alert alert-success">{{ session('success') }}</div>@endif
    <div class="panel">
        <table>
            <thead><tr><th>Date</th><th>Vehicle</th><th>Type</th><th>Reported by</th><th>Status</th><th></th></tr></thead>
            <tbody>
                @forelse ($reports as $report)
                    <tr>
                        <td>{{ $report->created_at }}</td>
                        <td>{{ $report->vehicle->vehicle_number }}</td>
                        <td>{{ ucfirst($report->type) }}</td>
                        <td>{{ $report->reporter->name }}</td>
                        <td>{{ ucfirst(str_replace('_', ' ', $report->status)) }}</td>
                        <td><a class="btn btn-ghost btn-sm" href="/vehicle-reports/{{ $report->id }}">View</a></td>
                    </tr>
                @empty
                    <tr><td colspan="6">No reports submitted.</td></tr>
                @endforelse
            </tbody>
        </table>
    </div>
    {{ $reports->links() }}
</x-app-layout>
