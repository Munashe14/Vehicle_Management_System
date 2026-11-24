<x-app-layout title="Edit Driver · Fleet Manager">
    <div class="page-header">
        <div>
            <span class="page-eyebrow">Driver Roster</span>
            <h1 class="page-title">Edit Driver</h1>
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
        <form action="/drivers/{{ $driver->id }}" method="POST">
            @csrf
            @method('PUT')

            <div class="field">
                <label>Full Name</label>
                <input name="name" value="{{ old('name', $driver->name) }}" required>
            </div>

            <div class="field">
                <label>Email</label>
                <input type="email" name="email" value="{{ old('email', $driver->email) }}" required>
            </div>

            <div class="field-row">
                <div class="field">
                    <label>Phone</label>
                    <input name="phone" value="{{ old('phone', $driver->phone ?? '') }}">
                </div>
                <div class="field">
                    <label>License Number</label>
                    <input name="license_number" value="{{ old('license_number', $driver->license_number ?? '') }}">
                </div>
            </div>

            <div class="form-actions">
                <button class="btn btn-primary" type="submit">Update Driver</button>
                <a href="/drivers" class="btn btn-ghost">Cancel</a>
            </div>
        </form>
    </div>
</x-app-layout>