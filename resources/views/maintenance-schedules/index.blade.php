<x-app-layout title="Schedules · Fleet Manager">
    <div class="page-header">
        <div>
            <span class="page-eyebrow">Maintenance</span>
            <h1 class="page-title">Schedules</h1>
        </div>
        @if (auth()->user()->hasPermission('maintenance.create'))<a href="/maintenance-schedules/create" class="btn btn-primary">+ New Schedule</a>@endif
    </div>

    @if (session('success'))
        <div class="alert alert-success">{{ session('success') }}</div>
    @endif

    <div class="panel">
        @if (($schedules ?? collect())->count() > 0)
            <table>
                <thead>
                <tr>
                    <th>Vehicle</th>
                    <th>Maintenance Type</th>
                    <th>Interval</th>
                    <th>Due Date</th>
                    <th>Actions</th>
                </tr>
                </thead>
                <tbody>
                @foreach ($schedules as $s)
                    @php
                        $isOverdue = ($s->due_date ?? null) && \Carbon\Carbon::parse($s->due_date)->isPast();
                    @endphp
                    <tr>
                        <td><span class="plate">{{ $s->vehicle->vehicle_number ?? $s->vehicle_id ?? '—' }}</span></td>
                        <td>{{ $s->type->name ?? $s->maintenance_type_id ?? $s->type_name ?? '—' }}</td>
                        <td>{{ $s->interval_value ?? '—' }}</td>
                        <td>
                            {{ $s->due_date ?? '—' }}
                            @if ($isOverdue)
                                <span class="status-pill status-pill--inactive" style="margin-left:6px;">overdue</span>
                            @endif
                        </td>
                        <td>
                            <div class="row-actions">
                                @if (auth()->user()->hasPermission('maintenance.update'))<a href="/maintenance-schedules/{{ $s->id }}/edit" class="btn btn-ghost btn-sm">Edit</a>@endif
                                @if (auth()->user()->hasPermission('maintenance.create'))<a href="/maintenance-events/create?maintenance_schedule_id={{ $s->id }}"
                                   class="btn btn-primary btn-sm">Record Event</a>@endif
                                @if (auth()->user()->hasPermission('maintenance.delete'))<form action="/maintenance-schedules/{{ $s->id }}" method="POST"
                                      onsubmit="return confirm('Delete this schedule?')">
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
                <div class="empty-state-title">No schedules set up</div>
                <p class="empty-state-body">Create a maintenance schedule to start tracking service intervals for your fleet.</p>
            </div>
        @endif
    </div>
</x-app-layout>
