<x-app-layout title="New Vehicle · Fleet Manager">
    <div class="page-header">
        <div>
            <span class="page-eyebrow">Fleet Registry</span>
            <h1 class="page-title">Register a Vehicle</h1>
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
        <form action="/vehicles" method="POST">
            @csrf

            <div class="field">
                <label>Vehicle Number</label>
                <input name="vehicle_number" value="{{ old('vehicle_number') }}" placeholder="e.g. FLT-014" required>
            </div>

            <div class="field-row">
                <div class="field">
                    <label>Make</label>
                    <input name="make" value="{{ old('make') }}" placeholder="e.g. Toyota">
                </div>
                <div class="field">
                    <label>Model</label>
                    <input name="model" value="{{ old('model') }}" placeholder="e.g. Hilux">
                </div>
            </div>

            <div class="field-row">
                <div class="field">
                    <label>Year</label>
                    <input name="year" value="{{ old('year') }}" placeholder="e.g. 2022">
                </div>
                <div class="field">
                    <label>Vehicle Type</label>
                    <input name="vehicle_type" value="{{ old('vehicle_type') }}" placeholder="e.g. Pickup">
                </div>
            </div>

            <div class="field-row">
                <div class="field">
                    <label>Capacity</label>
                    <input name="capacity" value="{{ old('capacity') }}" placeholder="e.g. 1000 kg">
                </div>
                <div class="field">
                    <label>Odometer Start</label>
                    <input name="odometer_start" value="{{ old('odometer_start') }}" placeholder="e.g. 0">
                </div>
            </div>

            <div class="field">
                <label>Status</label>
                <select name="status">
                    <option value="active" @selected(old('status') === 'active')>Active</option>
                    <option value="maintenance" @selected(old('status') === 'maintenance')>Maintenance</option>
                    <option value="inactive" @selected(old('status') === 'inactive')>Inactive</option>
                </select>
            </div>

            <div class="form-actions">
                <button class="btn btn-primary" type="submit">Save Vehicle</button>
                <a href="/vehicles" class="btn btn-ghost">Cancel</a>
            </div>
        </form>
    </div>
</x-app-layout>
