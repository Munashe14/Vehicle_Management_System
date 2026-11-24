<x-app-layout title="Submit Vehicle Report · Fleet Manager">
    <div class="page-header"><div><span class="page-eyebrow">Vehicle condition</span><h1 class="page-title">Submit a report</h1></div></div>
    @if ($errors->any())<div class="alert alert-error">{{ $errors->first() }}</div>@endif
    <div class="form-panel"><form method="POST" action="/vehicle-reports">@csrf
        <div class="field"><label>Vehicle</label><select name="vehicle_id" required>@foreach ($vehicles as $vehicle)<option value="{{ $vehicle->id }}">{{ $vehicle->vehicle_number }} — {{ $vehicle->make }} {{ $vehicle->model }}</option>@endforeach</select></div>
        <div class="field"><label>Report type</label><select name="type" required><option value="condition">Condition report</option><option value="defect">Defect / maintenance issue</option></select></div>
        <div class="field"><label>Details</label><textarea name="description" rows="5" minlength="5" maxlength="5000" required>{{ old('description') }}</textarea></div>
        <div class="form-actions"><button class="btn btn-primary">Submit report</button><a class="btn btn-ghost" href="/vehicle-reports">Cancel</a></div>
    </form></div>
</x-app-layout>
