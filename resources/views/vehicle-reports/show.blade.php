<x-app-layout title="Vehicle Report · Fleet Manager">
    <div class="page-header"><div><span class="page-eyebrow">Vehicle condition</span><h1 class="page-title">Report #{{ $vehicleReport->id }}</h1></div></div>
    @if (session('success'))<div class="alert alert-success">{{ session('success') }}</div>@endif
    @if ($errors->any())<div class="alert alert-error">{{ $errors->first() }}</div>@endif
    <div class="form-panel">
        <p><strong>Vehicle:</strong> {{ $vehicleReport->vehicle->vehicle_number }}</p>
        <p><strong>Type:</strong> {{ ucfirst($vehicleReport->type) }}</p>
        <p><strong>Reported by:</strong> {{ $vehicleReport->reporter->name }}</p>
        <p><strong>Submitted:</strong> {{ $vehicleReport->created_at }}</p>
        <p><strong>Details:</strong> {{ $vehicleReport->description }}</p>
        <p><strong>Status:</strong> {{ ucfirst(str_replace('_', ' ', $vehicleReport->status)) }}</p>
        @if ($vehicleReport->resolution)<p><strong>Resolution:</strong> {{ $vehicleReport->resolution }}</p>@endif
        @if (auth()->user()->hasPermission('vehicle_reports.update'))
            <form method="POST" action="/vehicle-reports/{{ $vehicleReport->id }}">@csrf @method('PUT')
                <div class="field"><label>Status</label><select name="status" required>@foreach (['open', 'in_progress', 'resolved'] as $status)<option value="{{ $status }}" @selected(old('status', $vehicleReport->status) === $status)>{{ ucfirst(str_replace('_', ' ', $status)) }}</option>@endforeach</select></div>
                <div class="field"><label>Resolution / review notes</label><textarea name="resolution" rows="4" maxlength="5000">{{ old('resolution', $vehicleReport->resolution) }}</textarea></div>
                <button class="btn btn-primary">Save review</button>
            </form>
        @endif
    </div>
</x-app-layout>
