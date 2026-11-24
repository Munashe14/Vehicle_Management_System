<x-app-layout title="Edit Maintenance Type · Fleet Manager">
    <div class="page-header">
        <div>
            <span class="page-eyebrow">Maintenance</span>
            <h1 class="page-title">Edit Maintenance Type</h1>
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
        <form action="/maintenance-types/{{ $type->id }}" method="POST">
            @csrf
            @method('PUT')

            <div class="field">
                <label>Name</label>
                <input name="name" value="{{ old('name', $type->name ?? '') }}" required>
            </div>

            <div class="field">
                <label>Description</label>
                <textarea name="description" rows="3">{{ old('description', $type->description ?? '') }}</textarea>
            </div>

            <div class="form-actions">
                <button class="btn btn-primary" type="submit">Update Type</button>
                <a href="/maintenance-types" class="btn btn-ghost">Cancel</a>
            </div>
        </form>
    </div>
</x-app-layout>
