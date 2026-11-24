<x-app-layout title="Trips · Fleet Manager">
    <div class="page-header">
        <div>
            <span class="page-eyebrow">Trip Log</span>
            <h1 class="page-title">Trips</h1>
        </div>
        @if (auth()->user()->hasPermission('trips.create'))<a href="/trips/create" class="btn btn-primary">+ New Trip</a>@endif
    </div>

    @if (session('success'))
        <div class="alert alert-success">{{ session('success') }}</div>
    @endif

    <div class="panel">
        @if (($trips ?? collect())->count() > 0)
            <table>
                <thead>
                <tr>
                    <th>Trip</th>
                    <th>Vehicle</th>
                    <th>Driver</th>
                    <th>Route</th>
                    <th>Date</th>
                    <th>Status</th>
                    <th>Actions</th>
                </tr>
                </thead>
                <tbody>
                @foreach ($trips as $t)
                    <tr>
                        <td><span class="plate">#{{ $t->id }}</span></td>
                        <td>{{ $t->vehicle->vehicle_number ?? $t->vehicle_id ?? '—' }}</td>
                        <td>{{ $t->driver->name ?? $t->driver_user_id ?? '—' }}</td>
                        <td>{{ $t->origin ?? '' }} &rarr; {{ $t->destination ?? '' }}</td>
                        <td>{{ $t->trip_date ?? (isset($t->created_at) ? $t->created_at->toDateString() : '—') }}</td>
                        <td>{{ ucfirst(str_replace('_', ' ', $t->status)) }}</td>
                        <td>
                            <div class="row-actions">
                                @if (auth()->user()->hasPermission('trips.update') && (!auth()->user()->isRole('driver') || $t->status !== 'completed'))<a href="/trips/{{ $t->id }}/edit" class="btn btn-ghost btn-sm">Edit</a>@endif
                                @if (auth()->user()->hasPermission('fuel.view'))<a href="/trips/{{ $t->id }}/fuel" class="btn btn-ghost btn-sm">Fuel</a>@endif
                                @if (auth()->user()->hasPermission('trips.delete'))<form action="/trips/{{ $t->id }}" method="POST"
                                      onsubmit="return confirm('Delete this trip?')">
                                    @csrf
                                    @method('DELETE')
                                    <button class="btn btn-danger btn-sm" type="submit">Delete</button>
                                </form>@endif
                            </div>
                        </td>
                    </tr>
                @endforeach
                </tbody>
            </table>
        @else
            <div class="empty-state">
                <div class="empty-state-title">No trips logged yet</div>
                <p class="empty-state-body">Record a trip to start tracking routes, mileage, and fuel use across your fleet.</p>
            </div>
        @endif
    </div>
</x-app-layout>
