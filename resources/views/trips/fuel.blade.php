<x-app-layout title="Fuel · Fleet Manager">
    <div class="page-header">
        <div>
            <span class="page-eyebrow">Trip Log</span>
            <h1 class="page-title">Fuel — Trip #{{ $trip->id }}</h1>
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

    <div class="panel" style="padding: 18px 20px; margin-bottom: 20px;">
        <div style="display:flex; gap:28px; flex-wrap:wrap; font-family: var(--font-mono); font-size: 12.5px; color: var(--text-muted);">
            <div>
                <div style="text-transform:uppercase; letter-spacing:0.05em; font-size:10.5px; margin-bottom:4px;">Vehicle</div>
                <span class="plate">{{ $trip->vehicle->vehicle_number ?? $trip->vehicle_id ?? '—' }}</span>
            </div>
            <div>
                <div style="text-transform:uppercase; letter-spacing:0.05em; font-size:10.5px; margin-bottom:4px;">Driver</div>
                <span style="color: var(--text);">{{ $trip->driver->name ?? $trip->driver_user_id ?? '—' }}</span>
            </div>
            <div>
                <div style="text-transform:uppercase; letter-spacing:0.05em; font-size:10.5px; margin-bottom:4px;">Route</div>
                <span style="color: var(--text);">{{ $trip->origin ?? '' }} &rarr; {{ $trip->destination ?? '' }}</span>
            </div>
        </div>
    </div>

    <div class="form-panel">
        @php
            $canEditFuel = auth()->user()->hasPermission('fuel.create')
                && (!$fuel || auth()->user()->hasPermission('fuel.update'));
        @endphp
        <form action="/trips/{{ $trip->id }}/fuel" method="POST">
            @csrf

            <div class="field">
                <label>Fuel Type</label>
                <input name="fuel_type" value="{{ old('fuel_type', $fuel->fuel_type ?? '') }}"
                       placeholder="diesel / petrol" required @readonly(!$canEditFuel)>
            </div>

            <div class="field-row">
                <div class="field">
                    <label>Consumed Liters</label>
                    <input id="consumed_liters" name="consumed_liters" type="number" step="0.01"
                           value="{{ old('consumed_liters', $fuel->consumed_liters ?? 0) }}" required @readonly(!$canEditFuel)>
                </div>
                <div class="field">
                    <label>Unit Cost</label>
                    <input id="unit_cost" name="unit_cost" type="number" step="0.01"
                           value="{{ old('unit_cost', $fuel->unit_cost ?? 0) }}" required @readonly(!$canEditFuel)>
                </div>
            </div>

            <input type="hidden" id="total_cost" name="total_cost"
                   value="{{ old('total_cost', $fuel->total_cost ?? 0) }}">

            <div style="background: var(--ink); border-radius: var(--radius); padding: 16px 18px; margin-bottom: 20px;">
                <div style="font-family: var(--font-mono); font-size: 10.5px; text-transform: uppercase; letter-spacing: 0.06em; color: #64748B; margin-bottom: 6px;">
                    Live Total
                </div>
                <div style="font-family: var(--font-mono); font-size: 26px; font-weight: 600; color: #F1F5F9;" id="total_cost_preview">
                    0.00
                </div>
                <div style="font-size: 11px; color: #64748B; margin-top: 4px;">
                    consumed liters &times; unit cost
                </div>
            </div>

            <div class="form-actions">
                @if ($canEditFuel)<button class="btn btn-primary" type="submit">Save Fuel Record</button>@endif
                <a href="/trips" class="btn btn-ghost">Back to Trips</a>
            </div>
        </form>
    </div>

    @push('scripts')
        <script src="{{ asset('js/trip-fuel.js') }}"></script>
    @endpush
</x-app-layout>
