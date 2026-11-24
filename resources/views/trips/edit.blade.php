<x-app-layout title="Edit Trip · Fleet Manager">
    <div class="page-header">
        <div>
            <span class="page-eyebrow">Trip Log</span>
            <h1 class="page-title">Edit Trip</h1>
        </div>
        <span class="plate">#{{ $trip->id }}</span>
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
        <form action="/trips/{{ $trip->id }}" method="POST">
            @csrf
            @method('PUT')

            @if (!auth()->user()->isRole('driver'))
            <div class="field">
                <label>Vehicle</label>
                <select name="vehicle_id" required>
                    <option value="">Select vehicle</option>
                    @foreach (($vehicles ?? []) as $v)
                        <option value="{{ $v->id }}" @selected(old('vehicle_id', $trip->vehicle_id ?? null) == $v->id)>
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
                        <option value="{{ $d->id }}" @selected(old('driver_user_id', $trip->driver_user_id ?? null) == $d->id)>
                            {{ $d->name ?? $d->id }}
                        </option>
                    @endforeach
                </select>
            </div>
            @endif

            <div class="field-row">
                <div class="field">
                    <label>Origin</label>
                    <input name="origin" value="{{ old('origin', $trip->origin ?? '') }}" required>
                </div>
                <div class="field">
                    <label>Destination</label>
                    <input name="destination" value="{{ old('destination', $trip->destination ?? '') }}" required>
                </div>
            </div>

            <div class="field">
                <label>Trip Date</label>
                <input type="date" name="trip_date" value="{{ old('trip_date', $trip->trip_date ?? '') }}">
            </div>

            @if (auth()->user()->isRole('driver'))
                <div class="field">
                    <label>Trip Status</label>
                    <select name="status">
                        <option value="{{ $trip->status }}" @selected(old('status', $trip->status) === $trip->status)>{{ ucfirst(str_replace('_', ' ', $trip->status)) }}</option>
                        @if ($trip->status === 'assigned')<option value="in_progress" @selected(old('status') === 'in_progress')>In progress</option>@endif
                        @if (in_array($trip->status, ['assigned', 'in_progress'], true))<option value="completed" @selected(old('status') === 'completed')>Completed</option>@endif
                    </select>
                </div>
            @else
                <div class="field">
                    <label>Trip Status</label>
                    <select name="status">
                        @foreach (['assigned', 'in_progress', 'completed'] as $status)
                            <option value="{{ $status }}" @selected(old('status', $trip->status) === $status)>{{ ucfirst(str_replace('_', ' ', $status)) }}</option>
                        @endforeach
                    </select>
                </div>
            @endif

            <div class="form-actions">
                <button class="btn btn-primary" type="submit">Update Trip</button>
                <a href="/trips" class="btn btn-ghost">Cancel</a>
            </div>
        </form>
    </div>
</x-app-layout>
