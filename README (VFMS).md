# Vehicle Management System

> Current repository note: this is an existing Laravel 8.83.29 application with
> controllers, models, migrations, authentication, and SQLite support. The
> historical scaffold instructions below are obsolete; do not create a second
> Laravel project or copy the existing code into a new one.

## Historical UI notes (superseded)

The notes in this section describe an earlier views-only snapshot and are kept
for reference. Follow the current setup and RBAC instructions below instead.

The code you pasted is **Laravel Blade** (`@csrf`, `x-app-layout`, `{{ }}`, `@foreach`),
not plain HTML/CSS/JS/PHP. Blade files are templates — they only render inside a
running **Laravel application**. They cannot be opened directly in a browser or
run with plain PHP (`php -S`) on their own.

I've split your one giant pasted block into a proper file structure and pulled
the inline `<script>` (fuel cost calculator) out into its own JS file, since you
asked for HTML/CSS/JS/PHP separation.

## Folder structure

```
vehicle-management/
├── resources/
│   └── views/
│       ├── components/
│       │   └── app-layout.blade.php     ← shared page shell (nav, <head>, scripts)
│       ├── vehicles/
│       │   ├── index.blade.php
│       │   ├── create.blade.php
│       │   └── edit.blade.php
│       ├── driver-assignments/
│       │   ├── index.blade.php
│       │   ├── create.blade.php
│       │   └── edit.blade.php
│       ├── trips/
│       │   ├── index.blade.php
│       │   ├── create.blade.php
│       │   ├── edit.blade.php
│       │   └── fuel.blade.php
│       ├── maintenance-types/
│       │   ├── index.blade.php
│       │   ├── create.blade.php
│       │   └── edit.blade.php
│       ├── maintenance-schedules/
│       │   ├── index.blade.php
│       │   ├── create.blade.php
│       │   └── edit.blade.php
│       └── maintenance-events/
│           └── create.blade.php
├── public/
│   ├── css/app.css                       ← extracted custom styles
│   └── js/
│       ├── app.js                        ← global JS
│       └── trip-fuel.js                  ← extracted fuel live-total calculator
├── routes/
│   └── web.php                           ← example routes matching the URLs used
└── README.md
```

## Obsolete scaffold checklist

The following checklist is obsolete: this repository already contains the
Laravel app, models, migrations, routes, and controllers it describes.

1. **A Laravel installation** (Laravel 10 or 11):
   ```bash
   composer create-project laravel/laravel fleet-app
   ```
2. **Copy these files in**:
   - Copy everything under `resources/views/` into your new project's `resources/views/`.
   - Copy everything under `public/css` and `public/js` into your project's `public/`.
   - Copy `routes/web.php` content into your project's `routes/web.php` (merge, don't overwrite the default one blindly).
3. **Models** — `Vehicle`, `DriverAssignment`, `Trip`, `Fuel`, `MaintenanceType`, `MaintenanceSchedule`, `MaintenanceEvent`, plus a `User` model for drivers.
4. **Migrations** matching the fields the views reference (e.g. `vehicles` table needs `vehicle_number`, `make`, `model`, `year`, `vehicle_type`, `capacity`, `odometer_start`, `status`).
5. **Controllers** with `index/create/store/edit/update/destroy` methods (I stubbed the route wiring in `routes/web.php`, but you still need the actual controller classes and logic).
6. **Database configured** in `.env` (SQLite is easiest to start with) and run:
   ```bash
   php artisan migrate
   ```
7. **Start the dev server**:
   ```bash
   php artisan serve
   ```
   Then visit `http://127.0.0.1:8000/vehicles`.

## Running on Acode (Android) or VS Code mobile

Blade/Laravel needs PHP + Composer + a database engine running — that's not
something Acode or VS Code's mobile editor can execute on their own; they're
editors, not servers. Practical options:
- Edit these files on your phone in Acode, then push to GitHub and run/test on
  a PC, a cloud dev environment (e.g. GitHub Codespaces), or a Linux-on-Android
  setup (Termux + PHP + Composer) that can actually run `php artisan serve`.
- If you want something that truly runs standalone on a phone with just a text
  editor and no server setup, that would need to be rewritten as plain HTML +
  vanilla JS (client-side only, no PHP/database) — a different, much smaller
  project than what's in this codebase.

Do not run the old project-creation or file-copy steps above for this repository.

## SQLite and role-based access

The application defaults to SQLite. With PHP's `pdo_sqlite` extension enabled,
run the following from the project directory:

```powershell
New-Item -ItemType File -Path database\database.sqlite -Force
php artisan migrate --seed
php artisan serve
```

Users can register at `/register`, choose one of the five roles, and sign in
immediately. Registration is rate limited, passwords must be at least 12
characters, and passwords are hashed. The selected role is assigned to the
account at registration; no administrator approval is required.

This direct role selection is intended only for the lecturer demonstration:
anyone who can reach the public registration page can select IT Manager and
receive administrator permissions. Do not use this configuration for a
production system with real users or sensitive data. For production, restore
administrator-controlled role assignment and approval before deployment.

All five roles use the same `/login` page with their own credentials.
Deactivated accounts cannot sign in. After credentials are verified, the
system loads the account's assigned role and redirects to its workspace:
IT Manager → user accounts, Fleet Supervisor → vehicles, Drivers → trips,
Finance Manager → financial report, and General Manager → fleet report. Use
`php artisan db:seed` to ensure the role list and permissions are present.

If you prefer to create an initial IT Manager from a trusted local shell,
`php artisan rbac:bootstrap-admin` remains available and prompts for the
account name, email and password.

The RBAC roles are **IT Manager**, **Fleet Supervisor**, **Driver**, **Finance
Manager**, and **General Manager**. Permissions are stored in the `permissions`
and `permission_role` tables and are explicitly seeded by `RbacSeeder`. Rerun
`php artisan db:seed` after changing the role-permission mapping in that seeder.
IT Managers can edit and deactivate accounts through the User Accounts screen.
For this presentation build, role selection is also available on public
registration. Security and operational changes are recorded in `audit_logs`;
denied authorization requests are logged as well.

| Role | Access summary |
|---|---|
| IT Manager | User and role assignment, audit log, all operational permissions |
| Fleet Supervisor | Fleet, drivers, allocations, trips, fuel, maintenance and operational reports |
| Driver | Own profile, assigned trips/vehicle, own condition and defect reports, fuel submission |
| Finance Manager | Read-only fuel, maintenance and financial reports |
| General Manager | Read-only fleet, trip, fuel, maintenance and expenditure reports |

Drivers can view their own profile and assigned trips. Trip record access is
filtered by the authenticated driver's user ID, and edits cannot change the
assigned driver or vehicle. Drivers can complete an assigned trip and cannot
edit it after completion. Drivers can also submit condition/defect reports for
their active vehicle assignment and view only their own report status. Finance
and management reports are read-only and support CSV download. Administrative
and operational routes enforce permissions on the server in addition to
hiding unavailable navigation/actions.

The existing application has no automated dashboard, maintenance alert
scheduler, or workflow for importing XAMPP data. These are not silently
simulated by the RBAC layer. The fleet/trip/fuel/maintenance/financial report
pages use the records already present in the database.

Changing the configured connection from XAMPP/MySQL to SQLite does not copy
records between databases. Existing MySQL data is left untouched; migrate it
separately if those records need to be available in SQLite. Before deployment,
set `APP_DEBUG=false`, generate a unique `APP_KEY`, and use a protected,
backed-up SQLite file with appropriate filesystem permissions.

## Docker deployment

The Docker image uses PHP 8.2 with Apache and SQLite. It serves Laravel from
`public/`, applies migrations and seeds RBAC roles/permissions at container
startup, and listens on the port provided by the hosting platform (8080 by
default).

Build and run locally:

```powershell
docker build --progress=plain -t vehicle-management .
docker run --name vfms -p 8000:8080 -e APP_ENV=production -e APP_DEBUG=false -e APP_KEY="base64:REPLACE_WITH_A_GENERATED_KEY" -e APP_URL=http://localhost:8000 -e DB_DATABASE=/var/lib/vfms/database.sqlite --mount source=vfms-data,target=/var/lib/vfms vehicle-management
```

Generate a unique key with `php artisan key:generate --show` and replace the
placeholder before starting the container. Keep the same key across restarts.
The named Docker volume stores SQLite data outside the image. After startup,
register an account and select **IT Manager / System Administrator** to access
the administrative screens.

For Render, create a Docker web service and set `APP_ENV=production`,
`APP_DEBUG=false`, `APP_KEY` (a generated key), and `APP_URL` (the public
service URL). Attach a persistent disk mounted at `/var/data`, then set
`DB_DATABASE=/var/data/database.sqlite`. The container runs migrations and
seeds RBAC roles/permissions on startup. Register an account and select IT
Manager to use the administrative screens. This direct role selection is for
demonstration only; do not expose this deployment as a production system.
Without persistent storage, SQLite data can be lost when the service is
replaced. Use a single running instance for this SQLite demonstration
deployment.

RBAC tests are run with:

```powershell
php artisan test --filter=RbacAccessTest
```
