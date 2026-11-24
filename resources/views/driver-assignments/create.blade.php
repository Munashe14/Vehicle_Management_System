<x-app-layout title="New Assignment · Fleet Manager">
    <div class="page-header">
        <div>
            <span class="page-eyebrow">Driver Roster</span>
            <h1 class="page-title">Assign a Driver</h1>
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
        <form action="/driver-assignments" method="POST">
            @csrf

            <div class="field">
                <label>Vehicle</label>
                <select name="vehicle_id" required>
                    <option value="">Select vehicle</option>
                    @foreach (($vehicles ?? []) as $v)
                        <option value="{{ $v->id }}" @selected(old('vehicle_id') == $v->id)>
                            {{ $v->vehicle_number ?? $v->id }}
                        </option>
                    @endforeach
                </select>
            </div>

            <div class="field">
                <label>Driver</label>
                <select name="driver_user_id" required>
                    <option value="">Select driver</option>
                    @foreach (($drivers ?? []) as $d)
                        <option value="{{ $d->id }}" @selected(old('driver_user_id') == $d->id)>
                            {{ $d->name ?? $d->id }}
                        </option>
                    @endforeach
                </select>
            </div>

            <div class="field-row">
                <div class="field">
                    <label>Assigned At</label>
                    <input type="date" name="assigned_at" value="{{ old('assigned_at') }}" required>
                </div>
                <div class="field">
                    <label>Released At</label>
                    <input type="date" name="released_at" value="{{ old('released_at') }}">
                    <span class="hint">Leave blank if still active</span>
                </div>
            </div>

            <div class="form-actions">
                <button class="btn btn-primary" type="submit">Save Assignment</button>
                <a href="/driver-assignments" class="btn btn-ghost">Cancel</a>
            </div>
        </form>
    </div>
</x-app-layout>
