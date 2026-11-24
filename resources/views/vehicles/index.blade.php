<x-app-layout title="Vehicles · Fleet Manager">
    <div class="page-header">
        <div>
            <span class="page-eyebrow">Fleet Registry</span>
            <h1 class="page-title">Vehicles</h1>
        </div>
        @if (auth()->user()->hasPermission('vehicles.create'))<a href="/vehicles/create" class="btn btn-primary">+ New Vehicle</a>@endif
    </div>

    @if (session('success'))
        <div class="alert alert-success">{{ session('success') }}</div>
    @endif

    @if ($errors->any())
        <div class="alert alert-error">
            <ul>
                @foreach ($errors->all() as $e)
                    <li>{{ $e }}</li>
                @endforeach
            </ul>
        </div>
    @endif

    <div class="panel">
        @if (($vehicles ?? collect())->count() > 0)
            <table>
                <thead>
                <tr>
                    <th>Number</th>
                    <th>Make / Model</th>
                    <th>Type</th>
                    <th>Capacity</th>
                    <th>Status</th>
                    <th>Actions</th>
                </tr>
                </thead>
                <tbody>
                @foreach ($vehicles as $vehicle)
                   @php
    $status = strtolower($vehicle->status ?? '');

    if ($status === 'active') {
        $statusClass = 'status-pill--active';
    } elseif ($status === 'maintenance' || $status === 'pending') {
        $statusClass = 'status-pill--pending';
    } elseif ($status === 'inactive' || $status === 'retired') {
        $statusClass = 'status-pill--inactive';
    } else {
        $statusClass = 'status-pill--neutral';
    }
@endphp
                    <tr>
                        <td><span class="plate">{{ $vehicle->vehicle_number ?? '—' }}</span></td>
                        <td>{{ $vehicle->make ?? '' }} {{ $vehicle->model ?? '' }}</td>
                        <td>{{ $vehicle->vehicle_type ?? '—' }}</td>
                        <td>{{ $vehicle->capacity ?? '—' }}</td>
                        <td>
                            <span class="status-pill {{ $statusClass }}">
                                {{ $vehicle->status ?? 'unknown' }}
                            </span>
                        </td>
                        <td>
                            <div class="row-actions">
                                @if (auth()->user()->hasPermission('vehicles.update'))<a href="/vehicles/{{ $vehicle->id }}/edit" class="btn btn-ghost btn-sm">Edit</a>@endif
                                @if (auth()->user()->hasPermission('vehicles.delete'))<form action="/vehicles/{{ $vehicle->id }}" method="POST"
                                      onsubmit="return confirm('Delete this vehicle?')">
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
                <div class="empty-state-title">No vehicles registered yet</div>
                <p class="empty-state-body">Add your first vehicle to start tracking assignments, trips, and maintenance for your fleet.</p>
            </div>
        @endif
    </div>
</x-app-layout>
