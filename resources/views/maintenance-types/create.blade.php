<x-app-layout title="New Maintenance Type · Fleet Manager">
    <div class="page-header">
        <div>
            <span class="page-eyebrow">Maintenance</span>
            <h1 class="page-title">New Maintenance Type</h1>
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
        <form action="/maintenance-types" method="POST">
            @csrf

            <div class="field">
                <label>Name</label>
                <input name="name" value="{{ old('name') }}" placeholder="e.g. Oil Change" required>
            </div>

            <div class="field">
                <label>Description</label>
                <textarea name="description" rows="3" placeholder="What does this maintenance involve?">{{ old('description') }}</textarea>
            </div>

            <div class="form-actions">
                <button class="btn btn-primary" type="submit">Save Type</button>
                <a href="/maintenance-types" class="btn btn-ghost">Cancel</a>
            </div>
        </form>
    </div>
</x-app-layout>
