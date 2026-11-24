<x-app-layout title="My Vehicle · Fleet Manager">
    <div class="page-header"><div><span class="page-eyebrow">My assignments</span><h1 class="page-title">Assigned vehicle</h1></div></div>
    @if ($assignment && $assignment->vehicle)
        <div class="form-panel">
            <p><strong>Vehicle:</strong> {{ $assignment->vehicle->vehicle_number }}</p>
            <p><strong>Make and model:</strong> {{ $assignment->vehicle->make }} {{ $assignment->vehicle->model }}</p>
            <p><strong>Type:</strong> {{ $assignment->vehicle->vehicle_type ?? '—' }}</p>
            <p><strong>Status:</strong> {{ $assignment->vehicle->status }}</p>
            <p><strong>Assigned:</strong> {{ $assignment->assigned_at }}</p>
        </div>
    @else
        <div class="empty-state"><div class="empty-state-title">No active vehicle assignment</div></div>
    @endif
</x-app-layout>
