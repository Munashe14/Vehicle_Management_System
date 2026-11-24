<x-app-layout title="Maintenance Types · Fleet Manager">
    <div class="page-header">
        <div>
            <span class="page-eyebrow">Maintenance</span>
            <h1 class="page-title">Maintenance Types</h1>
        </div>
        @if (auth()->user()->hasPermission('maintenance.create'))<a href="/maintenance-types/create" class="btn btn-primary">+ New Type</a>@endif
    </div>

    @if (session('success'))
        <div class="alert alert-success">{{ session('success') }}</div>
    @endif

    <div class="panel">
        @if ((($types ?? $maintenanceTypes ?? collect()))->count() > 0)
            <table>
                <thead>
                <tr>
                    <th>Name</th>
                    <th>Description</th>
                    <th>Actions</th>
                </tr>
                </thead>
                <tbody>
                @foreach (($types ?? $maintenanceTypes ?? []) as $type)
                    <tr>
                        <td><span class="plate">{{ $type->name ?? $type->type_name ?? $type->id }}</span></td>
                        <td>{{ $type->description ?? '—' }}</td>
                        <td>
                            <div class="row-actions">
                                @if (auth()->user()->hasPermission('maintenance.update'))<a href="/maintenance-types/{{ $type->id }}/edit" class="btn btn-ghost btn-sm">Edit</a>@endif
                                @if (auth()->user()->hasPermission('maintenance.delete'))<form action="/maintenance-types/{{ $type->id }}" method="POST"
                                      onsubmit="return confirm('Delete this type?')">
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
                <div class="empty-state-title">No maintenance types defined</div>
                <p class="empty-state-body">Add categories like oil changes or tire rotations to build maintenance schedules against.</p>
            </div>
        @endif
    </div>
</x-app-layout>
