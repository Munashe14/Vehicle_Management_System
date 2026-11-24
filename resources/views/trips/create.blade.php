<x-app-layout title="New Trip · Fleet Manager">
    <div class="page-header">
        <div>
            <span class="page-eyebrow">Trip Log</span>
            <h1 class="page-title">Record a Trip</h1>
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
        <form action="/trips" method="POST">
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
                    <label>Origin</label>
                    <input name="origin" value="{{ old('origin') }}" required>
                </div>
                <div class="field">
                    <label>Destination</label>
                    <input name="destination" value="{{ old('destination') }}" required>
                </div>
            </div>

            <div class="field">
                <label>Trip Date</label>
                <input type="date" name="trip_date" value="{{ old('trip_date') }}">
            </div>

            <div class="form-actions">
                <button class="btn btn-primary" type="submit">Save Trip</button>
                <a href="/trips" class="btn btn-ghost">Cancel</a>
            </div>
        </form>
    </div>
</x-app-layout>
