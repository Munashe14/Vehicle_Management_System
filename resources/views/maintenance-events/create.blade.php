<x-app-layout title="Record Maintenance · Fleet Manager">
    <div class="page-header">
        <div>
            <span class="page-eyebrow">Maintenance</span>
            <h1 class="page-title">Record Maintenance Event</h1>
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

    @php
        $scheduleId = $maintenanceSchedule->id ?? request()->query('maintenance_schedule_id');
    @endphp

    <div class="panel" style="padding: 18px 20px; margin-bottom: 20px;">
        <div style="display:flex; gap:28px; flex-wrap:wrap; font-family: var(--font-mono); font-size: 12.5px; color: var(--text-muted);">
            <div>
                <div style="text-transform:uppercase; letter-spacing:0.05em; font-size:10.5px; margin-bottom:4px;">Schedule</div>
                <span class="plate">{{ $scheduleId ?? '—' }}</span>
            </div>
            <div>
                <div style="text-transform:uppercase; letter-spacing:0.05em; font-size:10.5px; margin-bottom:4px;">Vehicle</div>
                <span style="color: var(--text);">{{ $maintenanceSchedule->vehicle->vehicle_number ?? $maintenanceSchedule->vehicle_id ?? '—' }}</span>
            </div>
            <div>
                <div style="text-transform:uppercase; letter-spacing:0.05em; font-size:10.5px; margin-bottom:4px;">Type</div>
                <span style="color: var(--text);">{{ $maintenanceSchedule->type->name ?? $maintenanceSchedule->maintenance_type_id ?? '—' }}</span>
            </div>
        </div>
    </div>

    <div class="form-panel">
        <form action="/maintenance-events" method="POST">
            @csrf
            <input type="hidden" name="maintenance_schedule_id" value="{{ $scheduleId }}">

            <div class="field">
                <label>Work Details</label>
                <textarea name="work_details" rows="4" placeholder="What was done during this service?" required>{{ old('work_details') }}</textarea>
            </div>

            <div class="field-row">
                <div class="field">
                    <label>Cost</label>
                    <input name="cost" type="number" step="0.01" value="{{ old('cost') }}" required>
                </div>
                <div class="field">
                    <label>Completed At</label>
                    <input type="date" name="completed_at" value="{{ old('completed_at') }}" required>
                </div>
            </div>

            <div class="field">
                <label>Status</label>
                @php($statuses = ['completed','pending','cancelled','in_progress'])
                <select name="status" required>
                    <option value="">Select status</option>
                    @foreach ($statuses as $st)
                        <option value="{{ $st }}" @selected(old('status') === $st)>{{ ucfirst(str_replace('_',' ',$st)) }}</option>
                    @endforeach
                </select>
            </div>

            <div class="form-actions">
                <button class="btn btn-primary" type="submit">Save Event</button>
                <a href="/maintenance-schedules" class="btn btn-ghost">Back</a>
            </div>
        </form>
    </div>
</x-app-layout>
