<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>{{ $title ?? 'Fleet Manager' }}</title>

    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Archivo:wght@500;600;700;800&family=IBM+Plex+Sans:wght@400;500;600&family=IBM+Plex+Mono:wght@400;500;600&display=swap" rel="stylesheet">

    {{-- Tailwind stays available for layout utilities (flex/spacing) used inside pages;
         app.css below defines the actual visual system and takes precedence. --}}
    <script src="https://cdn.tailwindcss.com"></script>
    <link rel="stylesheet" href="{{ asset('css/app.css') }}">
</head>
<body>
    <div class="shell">
        <aside class="sidebar">
            <div class="sidebar-brand">
                <span class="sidebar-brand-mark">FM</span>
                <div class="sidebar-brand-text">
                    <span class="sidebar-brand-name">Fleet Manager</span>
                    <span class="sidebar-brand-sub">Operations Console</span>
                </div>
            </div>

            <nav class="sidebar-nav">
                @php
                    $current = trim(request()->path(), '/');
                    $section = explode('/', $current)[0] ?? '';
                @endphp

                @if (auth()->user()->hasPermission('vehicles.view'))
                    <a href="/vehicles" class="sidebar-link {{ $section === 'vehicles' ? 'is-active' : '' }}"><span class="sidebar-link-dot"></span>Vehicles</a>
                @endif
                @if (auth()->user()->hasPermission('drivers.view'))
                    <a href="/drivers" class="sidebar-link {{ $section === 'drivers' ? 'is-active' : '' }}"><span class="sidebar-link-dot"></span>Drivers</a>
                @endif
                @if (auth()->user()->hasPermission('allocations.view'))
                    <a href="/driver-assignments" class="sidebar-link {{ $section === 'driver-assignments' ? 'is-active' : '' }}"><span class="sidebar-link-dot"></span>Assignments</a>
                @endif
                @if (auth()->user()->hasPermission('trips.view'))
                    <a href="/trips" class="sidebar-link {{ $section === 'trips' ? 'is-active' : '' }}"><span class="sidebar-link-dot"></span>Trips</a>
                @endif
                @if (auth()->user()->hasPermission('maintenance.view'))
                    <a href="/maintenance-types" class="sidebar-link {{ $section === 'maintenance-types' ? 'is-active' : '' }}"><span class="sidebar-link-dot"></span>Maintenance Types</a>
                    <a href="/maintenance-schedules" class="sidebar-link {{ $section === 'maintenance-schedules' ? 'is-active' : '' }}"><span class="sidebar-link-dot"></span>Schedules</a>
                @endif
                @if (auth()->user()->hasPermission('reports.fleet'))<a href="/reports/fleet" class="sidebar-link {{ $current === 'reports/fleet' ? 'is-active' : '' }}"><span class="sidebar-link-dot"></span>Fleet Report</a>@endif
                @if (auth()->user()->hasPermission('reports.trips'))<a href="/reports/trips" class="sidebar-link {{ $current === 'reports/trips' ? 'is-active' : '' }}"><span class="sidebar-link-dot"></span>Trip Report</a>@endif
                @if (auth()->user()->hasPermission('reports.fuel'))<a href="/reports/fuel" class="sidebar-link {{ $current === 'reports/fuel' ? 'is-active' : '' }}"><span class="sidebar-link-dot"></span>Fuel Report</a>@endif
                @if (auth()->user()->hasPermission('reports.maintenance'))<a href="/reports/maintenance" class="sidebar-link {{ $current === 'reports/maintenance' ? 'is-active' : '' }}"><span class="sidebar-link-dot"></span>Maintenance Report</a>@endif
                @if (auth()->user()->hasPermission('reports.financial'))<a href="/reports/financial" class="sidebar-link {{ $current === 'reports/financial' ? 'is-active' : '' }}"><span class="sidebar-link-dot"></span>Financial Report</a>@endif
                @if (auth()->user()->hasPermission('users.view'))
                    <a href="/users" class="sidebar-link {{ $section === 'users' ? 'is-active' : '' }}"><span class="sidebar-link-dot"></span>User Accounts</a>
                @endif
                @if (auth()->user()->hasPermission('audit.view'))
                    <a href="/audit-logs" class="sidebar-link {{ $section === 'audit-logs' ? 'is-active' : '' }}"><span class="sidebar-link-dot"></span>Audit Log</a>
                @endif
                @if (auth()->user()->hasPermission('profile.view'))
                    <a href="/profile" class="sidebar-link {{ $section === 'profile' ? 'is-active' : '' }}"><span class="sidebar-link-dot"></span>My Profile</a>
                    @if (auth()->user()->isRole('driver'))
                        <a href="/my-vehicle" class="sidebar-link {{ $current === 'my-vehicle' ? 'is-active' : '' }}"><span class="sidebar-link-dot"></span>My Vehicle</a>
                    @endif
                @endif
                @if (auth()->user()->hasPermission('vehicle_reports.view'))
                    <a href="/vehicle-reports" class="sidebar-link {{ $section === 'vehicle-reports' ? 'is-active' : '' }}"><span class="sidebar-link-dot"></span>Vehicle Reports</a>
                @endif
            </nav>

            <div class="sidebar-footer">
                <span class="sidebar-footer-label">{{ auth()->user()->role->label ?? 'No role assigned' }}</span>
                <form method="POST" action="{{ route('logout') }}">@csrf<button class="btn btn-ghost" type="submit">Sign out</button></form>
                <span class="sidebar-footer-label">System status</span>
                <span class="status-pill status-pill--active">Operational</span>
            </div>
        </aside>

        <div class="main-column">
            <main class="main-content">
                {{ $slot }}
            </main>
        </div>
    </div>

    <script src="{{ asset('js/app.js') }}"></script>
    @stack('scripts')
</body>
</html>
