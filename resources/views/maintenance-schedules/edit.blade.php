<x-app-layout title="Edit Schedule · Fleet Manager">
    <div class="page-header">
        <div>
            <span class="page-eyebrow">Maintenance</span>
            <h1 class="page-title">Edit Schedule</h1>
        </div>
    </div>

    @if ($errors->any())
        <div class="alert alert-error">
            <ul>
                @foreach ($errors->all() as $e)
                    <li>{{ $e }}</li>
                @endforeach
            </ul>
        </div>
    @endif

    <div class="form-panel">
        <form action="/maintenance-schedules/{{ $schedule->id }}" method="POST">
            @csrf
            @method('PUT')

            <div class="field">
                <label>Vehicle</label>
                <select name="vehicle_id" required>
                    <option value="">Select vehicle</option>
                    @foreach (($vehicles ?? []) as $v)
                        <option value="{{ $v->id }}"
                                @selected(old('vehicle_id', $schedule->vehicle_id ?? null) == $v->id)>{{ $v->vehicle_number ?? $v->id }}</option>
                    @endforeach
                </select>
            </div>

            <div class="field">
                <label>Maintenance Type</label>
                <select name="maintenance_type_id" required>
                    <option value="">Select type</option>
                    @foreach (($types ?? $maintenanceTypes ?? []) as $t)
                        <option value="{{ $t->id }}"
                                @selected(old('maintenance_type_id', $schedule->maintenance_type_id ?? null) == $t->id)>{{ $t->name ?? $t->id }}</option>
                    @endforeach
                </select>
            </div>

            <div class="field-row">
                <div class="field">
                    <label>Interval</label>
                    <input name="interval_value" value="{{ old('interval_value', $schedule->interval_value ?? '') }}" type="number" step="1" required>
                    <span class="hint">days between services</span>
                </div>
                <div class="field">
                    <label>Due Date</label>
                    <input type="date" name="due_date" value="{{ old('due_date', $schedule->due_date ?? '') }}" required>
                </div>
            </div>

            <div class="form-actions">
                <button class="btn btn-primary" type="submit">Update Schedule</button>
                <a href="/maintenance-schedules" class="btn btn-ghost">Cancel</a>
            </div>
        </form>
    </div>
</x-app-layout>
