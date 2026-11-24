<x-app-layout title="Assignments · Fleet Manager">
    <div class="page-header">
        <div>
            <span class="page-eyebrow">Driver Roster</span>
            <h1 class="page-title">Assignments</h1>
        </div>
        @if (auth()->user()->hasPermission('allocations.create'))<a href="/driver-assignments/create" class="btn btn-primary">+ New Assignment</a>@endif
    </div>

    @if (session('success'))
        <div class="alert alert-success">{{ session('success') }}</div>
    @endif

    <div class="panel">
        @if (($assignments ?? collect())->count() > 0)
            <table>
                <thead>
                <tr>
                    <th>Vehicle</th>
                    <th>Driver</th>
                    <th>Assigned</th>
                    <th>Released</th>
                    <th>Actions</th>
                </tr>
                </thead>
                <tbody>
                @foreach ($assignments as $a)
                    <tr>
                        <td><span class="plate">{{ $a->vehicle->vehicle_number ?? $a->vehicle_id ?? '—' }}</span></td>
                        <td>{{ $a->driver->name ?? $a->driver_user_id ?? '—' }}</td>
                        <td>{{ $a->assigned_at ?? '—' }}</td>
                        <td>
                            @if ($a->released_at ?? null)
                                {{ $a->released_at }}
                            @else
                                <span class="status-pill status-pill--active">ongoing</span>
                            @endif
                        </td>
                        <td>
                            <div class="row-actions">
                                @if (auth()->user()->hasPermission('allocations.update'))<a href="/driver-assignments/{{ $a->id }}/edit" class="btn btn-ghost btn-sm">Edit</a>@endif
                                @if (auth()->user()->hasPermission('allocations.delete'))<form action="/driver-assignments/{{ $a->id }}" method="POST"
                                      onsubmit="return confirm('Delete this assignment?')">
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
                <div class="empty-state-title">No assignments yet</div>
                <p class="empty-state-body">Assign a driver to a vehicle to start tracking who's operating what.</p>
            </div>
        @endif
    </div>
</x-app-layout>
