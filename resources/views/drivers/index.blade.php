<x-app-layout title="Drivers · Fleet Manager">
    <div class="page-header">
        <div>
            <span class="page-eyebrow">Driver Roster</span>
            <h1 class="page-title">Drivers</h1>
        </div>
        @if (auth()->user()->hasPermission('drivers.create'))<a href="/drivers/create" class="btn btn-primary">+ New Driver</a>@endif
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
        @if (($drivers ?? collect())->count() > 0)
            <table>
                <thead>
                <tr>
                    <th>Name</th>
                    <th>Email</th>
                    <th>Phone</th>
                    <th>License No.</th>
                    <th>Actions</th>
                </tr>
                </thead>
                <tbody>
                @foreach ($drivers as $driver)
                    <tr>
                        <td>{{ $driver->name }}</td>
                        <td>{{ $driver->email }}</td>
                        <td>{{ $driver->phone ?? '—' }}</td>
                        <td>
                            @if ($driver->license_number)
                                <span class="plate">{{ $driver->license_number }}</span>
                            @else
                                —
                            @endif
                        </td>
                        <td>
                            <div class="row-actions">
                                @if (auth()->user()->hasPermission('drivers.update'))<a href="/drivers/{{ $driver->id }}/edit" class="btn btn-ghost btn-sm">Edit</a>@endif
                                @if (auth()->user()->hasPermission('drivers.delete'))<form action="/drivers/{{ $driver->id }}" method="POST"
                                      onsubmit="return confirm('Remove this driver?')">
                                    @csrf
                                    @method('DELETE')
                                    <button class="btn btn-danger btn-sm" type="submit">Remove</button>
                                </form>@endif
                            </div>
                        </td>
                    </tr>
                @endforeach
                </tbody>
            </table>
        @else
            <div class="empty-state">
                <div class="empty-state-title">No drivers on record</div>
                <p class="empty-state-body">Add a driver so you can assign them to vehicles and trips.</p>
            </div>
        @endif
    </div>
</x-app-layout>