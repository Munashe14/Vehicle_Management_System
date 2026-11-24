<x-app-layout title="New Driver · Fleet Manager">
    <div class="page-header">
        <div>
            <span class="page-eyebrow">Driver Roster</span>
            <h1 class="page-title">Add a Driver</h1>
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
        <form action="/drivers" method="POST">
            @csrf

            <div class="field">
                <label>Full Name</label>
                <input name="name" value="{{ old('name') }}" placeholder="e.g. Tendai Moyo" required>
            </div>

            <div class="field">
                <label>Email</label>
                <input type="email" name="email" value="{{ old('email') }}" placeholder="driver@example.com" required>
            </div>

            <div class="field-row">
                <div class="field">
                    <label>Phone</label>
                    <input name="phone" value="{{ old('phone') }}" placeholder="e.g. 077 123 4567">
                </div>
                <div class="field">
                    <label>License Number</label>
                    <input name="license_number" value="{{ old('license_number') }}" placeholder="e.g. DL-00123">
                </div>
            </div>

            <div class="form-actions">
                <button class="btn btn-primary" type="submit">Save Driver</button>
                <a href="/drivers" class="btn btn-ghost">Cancel</a>
            </div>
        </form>
    </div>
</x-app-layout>