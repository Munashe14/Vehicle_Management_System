<x-app-layout title="New Schedule · Fleet Manager">
    <div class="page-header">
        <div>
            <span class="page-eyebrow">Maintenance</span>
            <h1 class="page-title">New Schedule</h1>
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
        <form action="/maintenance-schedules" method="POST">
            @csrf

            <div class="field">
                <label>Vehicle</label>
                <select name="vehicle_id" required>
                    <option value="">Select vehicle</option>
                    @foreach (($vehicles ?? []) as $v)
                        <option value="{{ $v->id }}" @selected(old('vehicle_id') == $v->id)>{{ $v->vehicle_number ?? $v->id }}</option>
                    @endforeach
                </select>
            </div>

            <div class="field">
                <label>Maintenance Type</label>
                <select name="maintenance_type_id" required>
                    <option value="">Select type</option>
                    @foreach (($types ?? $maintenanceTypes ?? []) as $t)
                        <option value="{{ $t->id }}" @selected(old('maintenance_type_id') == $t->id)>{{ $t->name ?? $t->id }}</option>
                    @endforeach
                </select>
            </div>

            <div class="field-row">
                <div class="field">
                    <label>Interval</label>
                    <input name="interval_value" value="{{ old('interval_value') }}" type="number" step="1" placeholder="e.g. 30" required>
                    <span class="hint">days between services</span>
                </div>
                <div class="field">
                    <label>Due Date</label>
                    <input type="date" name="due_date" value="{{ old('due_date') }}" required>
                </div>
            </div>

            <div class="form-actions">
                <button class="btn btn-primary" type="submit">Save Schedule</button>
                <a href="/maintenance-schedules" class="btn btn-ghost">Cancel</a>
            </div>
        </form>
    </div>
</x-app-layout>
