<x-app-layout title="Edit Vehicle · Fleet Manager">
    <div class="page-header">
        <div>
            <span class="page-eyebrow">Fleet Registry</span>
            <h1 class="page-title">Edit Vehicle</h1>
        </div>
        <span class="plate">{{ $vehicle->vehicle_number ?? '' }}</span>
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
        <form action="/vehicles/{{ $vehicle->id }}" method="POST">
            @csrf
            @method('PUT')

            <div class="field">
                <label>Vehicle Number</label>
                <input name="vehicle_number" value="{{ old('vehicle_number', $vehicle->vehicle_number ?? '') }}" required>
            </div>

            <div class="field-row">
                <div class="field">
                    <label>Make</label>
                    <input name="make" value="{{ old('make', $vehicle->make ?? '') }}">
                </div>
                <div class="field">
                    <label>Model</label>
                    <input name="model" value="{{ old('model', $vehicle->model ?? '') }}">
                </div>
            </div>

            <div class="field-row">
                <div class="field">
                    <label>Year</label>
                    <input name="year" value="{{ old('year', $vehicle->year ?? '') }}">
                </div>
                <div class="field">
                    <label>Vehicle Type</label>
                    <input name="vehicle_type" value="{{ old('vehicle_type', $vehicle->vehicle_type ?? '') }}">
                </div>
            </div>

            <div class="field-row">
                <div class="field">
                    <label>Capacity</label>
                    <input name="capacity" value="{{ old('capacity', $vehicle->capacity ?? '') }}">
                </div>
                <div class="field">
                    <label>Odometer Start</label>
                    <input name="odometer_start" value="{{ old('odometer_start', $vehicle->odometer_start ?? '') }}">
                </div>
            </div>

            <div class="field">
                <label>Status</label>
                @php($currentStatus = old('status', $vehicle->status ?? 'active'))
                <select name="status">
                    <option value="active" @selected($currentStatus === 'active')>Active</option>
                    <option value="maintenance" @selected($currentStatus === 'maintenance')>Maintenance</option>
                    <option value="inactive" @selected($currentStatus === 'inactive')>Inactive</option>
                </select>
            </div>

            <div class="form-actions">
                <button class="btn btn-primary" type="submit">Update Vehicle</button>
                <a href="/vehicles" class="btn btn-ghost">Cancel</a>
            </div>
        </form>
    </div>
</x-app-layout>
