<?php

namespace Tests\Feature;

use App\Models\Role;
use App\Models\AuditLog;
use App\Models\DriverAssignment;
use App\Models\Trip;
use App\Models\User;
use App\Models\Vehicle;
use App\Models\VehicleReport;
use Database\Seeders\RbacSeeder;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Hash;
use Tests\TestCase;

class RbacAccessTest extends TestCase
{
    use RefreshDatabase;

    protected function setUp(): void
    {
        parent::setUp();
        $this->seed(RbacSeeder::class);
    }

    public function test_unauthenticated_users_are_redirected_to_login()
    {
        $this->get('/vehicles')->assertRedirect('/login');
    }

    public function test_one_time_admin_setup_and_approval_routes_are_removed()
    {
        $this->get('/initial-admin')->assertNotFound();
        $this->post('/initial-admin')->assertNotFound();
        $this->patch('/users/1/approve')->assertNotFound();
        $this->patch('/users/1/reject')->assertNotFound();
    }

    public function test_audit_log_displays_readable_actions_and_formatted_details()
    {
        $admin = $this->userWithRole('it_manager');
        AuditLog::create([
            'user_id' => $admin->id,
            'action' => 'user.registration_requested',
            'subject_type' => User::class,
            'subject_id' => $admin->id,
            'details' => [
                'requested_role' => 'it_manager',
                'active' => true,
            ],
        ]);

        $this->actingAs($admin)->get('/audit-logs')
            ->assertOk()
            ->assertSee('Registration requested')
            ->assertSee('Requested Role')
            ->assertSee('IT Manager')
            ->assertSee('Active')
            ->assertSee('Yes')
            ->assertDontSee('user.registration_requested')
            ->assertDontSee('&quot;requested_role&quot;');
    }

    public function test_login_requires_an_active_account_with_an_assigned_role()
    {
        $user = $this->userWithRole('driver');

        $this->post('/login', [
            'email' => $user->email,
            'password' => 'Correct-Horse-Battery-1',
        ])->assertRedirect(route('home'));
        $this->assertAuthenticatedAs($user);
        $this->get('/login')->assertRedirect('/');

        auth()->logout();
        $user->is_active = false;
        $user->save();
        $this->post('/login', [
            'email' => $user->email,
            'password' => 'Correct-Horse-Battery-1',
        ])->assertSessionHasErrors('email');
        $this->assertGuest();
        $this->actingAs($user)->get('/vehicles')->assertRedirect('/login');
        $this->assertGuest();
    }

    public function test_all_five_roles_can_sign_in_and_land_on_their_role_home()
    {
        $landingPages = [
            'it_manager' => '/users',
            'fleet_supervisor' => '/vehicles',
            'driver' => '/trips',
            'finance_manager' => '/reports/financial',
            'general_manager' => '/reports/fleet',
        ];

        foreach ($landingPages as $roleName => $path) {
            $user = $this->userWithRole($roleName);

            $this->post('/login', [
                'email' => $user->email,
                'password' => 'Correct-Horse-Battery-1',
            ])->assertRedirect(route('home'));

            $this->assertAuthenticatedAs($user);
            $this->get('/')->assertRedirect($path);
            $this->post('/logout')->assertRedirect(route('login'));
            $this->assertGuest();
        }
    }

    public function test_registration_immediately_activates_the_account_with_the_selected_role()
    {
        $financeRole = Role::where('name', 'finance_manager')->firstOrFail();
        $itRole = Role::where('name', 'it_manager')->firstOrFail();
        $this->get('/register')->assertOk()
            ->assertSee('Create your account')
            ->assertSee('name="role_id"', false)
            ->assertSee('Your account will be active immediately');

        $this->post('/register', [
            'name' => 'New Finance Manager',
            'email' => '  DRIVER@EXAMPLE.TEST ',
            'phone' => '0770000000',
            'license_number' => 'DL-7788',
            'role_id' => $financeRole->id,
            'password' => 'Strong-Driver-Password-123',
            'password_confirmation' => 'Strong-Driver-Password-123',
            'requested_role_id' => $itRole->id,
        ])->assertRedirect(route('login'));

        $user = User::where('email', 'driver@example.test')->firstOrFail();
        $this->assertGuest();
        $this->assertTrue($user->is_active);
        $this->assertSame($financeRole->id, $user->role_id);
        $this->assertNull($user->requested_role_id);
        $this->assertSame('approved', $user->registration_status);
        $this->assertFalse($user->hasPermission('users.create'));
        $this->assertTrue(Hash::check('Strong-Driver-Password-123', $user->password));
        $this->assertDatabaseHas('audit_logs', [
            'user_id' => $user->id,
            'action' => 'user.self_registered',
            'subject_id' => $user->id,
        ]);

        $this->post('/login', [
            'email' => $user->email,
            'password' => 'Strong-Driver-Password-123',
        ])->assertRedirect(route('home'));
        $this->assertAuthenticatedAs($user);
        $this->get('/')->assertRedirect('/reports/financial');

        $this->post('/logout')->assertRedirect(route('login'));
        $this->post('/register', [
            'name' => 'Initial Manager',
            'email' => 'initial-manager@example.test',
            'role_id' => $itRole->id,
            'password' => 'Strong-Manager-Password-123',
            'password_confirmation' => 'Strong-Manager-Password-123',
        ])->assertRedirect(route('login'));

        $admin = User::where('email', 'initial-manager@example.test')->firstOrFail();
        $this->assertTrue($admin->is_active);
        $this->assertTrue($admin->isRole('it_manager'));
        $this->assertFalse($admin->hasPermission('users.approve'));
        $this->assertTrue($admin->hasPermission('users.create'));
    }

    public function test_legacy_pending_registration_can_sign_in_with_its_requested_role()
    {
        $user = User::create([
            'name' => 'Pending Supervisor',
            'email' => 'legacy-supervisor@example.test',
            'password' => Hash::make('Correct-Horse-Battery-1'),
            'requested_role_id' => Role::where('name', 'fleet_supervisor')->value('id'),
            'registration_status' => 'pending',
            'is_active' => false,
        ]);

        $this->post('/login', [
            'email' => $user->email,
            'password' => 'Correct-Horse-Battery-1',
        ])->assertRedirect(route('home'));

        $user->refresh();
        $this->assertTrue($user->is_active);
        $this->assertTrue($user->isRole('fleet_supervisor'));
        $this->assertNull($user->requested_role_id);
        $this->assertSame('approved', $user->registration_status);
    }

    public function test_registration_rejects_duplicate_driver_license_numbers()
    {
        $this->userWithRole('driver', 'existing-driver@example.test')->update([
            'license_number' => 'DL-DUPLICATE',
        ]);

        $this->from('/register')->post('/register', [
            'name' => 'Another Driver',
            'email' => 'another@example.test',
            'license_number' => 'DL-DUPLICATE',
            'password' => 'Strong-Driver-Password-123',
            'password_confirmation' => 'Strong-Driver-Password-123',
        ])->assertSessionHasErrors('license_number');

        $this->assertDatabaseMissing('users', ['email' => 'another@example.test']);
    }

    public function test_general_manager_can_view_but_cannot_change_vehicle_records()
    {
        $manager = $this->userWithRole('general_manager');
        $vehicle = Vehicle::create(['vehicle_number' => 'TR-001']);

        $this->actingAs($manager)->get('/vehicles')->assertOk();
        $this->actingAs($manager)->get('/vehicles/create')->assertForbidden();
        $this->actingAs($manager)->put('/vehicles/'.$vehicle->id, [
            'vehicle_number' => 'TR-002',
            'status' => 'active',
        ])->assertForbidden();

        $this->assertDatabaseHas('vehicles', ['id' => $vehicle->id, 'vehicle_number' => 'TR-001']);
    }

    public function test_driver_can_only_view_and_update_their_assigned_trips()
    {
        $driver = $this->userWithRole('driver');
        $otherDriver = $this->userWithRole('driver', 'other@example.test');
        $vehicle = Vehicle::create(['vehicle_number' => 'TR-003']);
        $otherVehicle = Vehicle::create(['vehicle_number' => 'TR-004']);
        $ownTrip = $this->trip($vehicle, $driver);
        $otherTrip = $this->trip($otherVehicle, $otherDriver);

        $this->actingAs($driver)->get('/trips')->assertOk()->assertSee('#'.$ownTrip->id)->assertDontSee('#'.$otherTrip->id);
        $this->actingAs($driver)->get('/trips/'.$ownTrip->id.'/edit')->assertOk()->assertDontSee('name="vehicle_id"');
        $this->actingAs($driver)->get('/trips/'.$otherTrip->id.'/edit')->assertNotFound();

        $this->actingAs($driver)->put('/trips/'.$ownTrip->id, [
            'origin' => 'Depot',
            'destination' => 'Customer',
            'trip_date' => '2026-11-24',
            'driver_user_id' => $otherDriver->id,
            'vehicle_id' => $otherVehicle->id,
        ])->assertRedirect('/trips');

        $this->assertDatabaseHas('trips', [
            'id' => $ownTrip->id,
            'driver_user_id' => $driver->id,
            'vehicle_id' => $vehicle->id,
            'origin' => 'Depot',
        ]);

        $this->actingAs($driver)->put('/trips/'.$ownTrip->id, [
            'origin' => 'Depot',
            'destination' => 'Customer',
            'trip_date' => '2026-11-24',
            'status' => 'completed',
        ])->assertRedirect('/trips');
        $this->actingAs($driver)->put('/trips/'.$ownTrip->id, [
            'origin' => 'Changed after completion',
            'destination' => 'Customer',
            'status' => 'in_progress',
        ])->assertForbidden();
    }

    public function test_finance_manager_can_view_authorized_reports_but_cannot_change_fuel_data()
    {
        $finance = $this->userWithRole('finance_manager');
        $driver = $this->userWithRole('driver');
        $vehicle = Vehicle::create(['vehicle_number' => 'TR-005']);
        $trip = $this->trip($vehicle, $driver);

        $this->actingAs($finance)->get('/reports/fuel')->assertOk();
        $this->actingAs($finance)->get('/trips/'.$trip->id.'/fuel')->assertOk();
        $this->actingAs($finance)->get('/vehicles')->assertForbidden();
        $this->actingAs($finance)->post('/trips/'.$trip->id.'/fuel', [
            'fuel_type' => 'diesel',
            'consumed_liters' => 10,
            'unit_cost' => 2,
        ])->assertForbidden();
    }

    public function test_last_active_it_manager_cannot_be_reassigned_or_deactivated()
    {
        $admin = $this->userWithRole('it_manager');
        $generalManager = Role::where('name', 'general_manager')->firstOrFail();

        $this->actingAs($admin)->put('/users/'.$admin->id, [
            'name' => $admin->name,
            'email' => $admin->email,
            'role_id' => $generalManager->id,
        ])->assertStatus(422);

        $this->actingAs($admin)->patch('/users/'.$admin->id.'/deactivate')->assertSessionHasErrors();
        $this->assertDatabaseHas('users', ['id' => $admin->id, 'is_active' => true]);
    }

    public function test_only_it_managers_can_create_user_accounts_and_passwords_are_hashed()
    {
        $admin = $this->userWithRole('it_manager');
        $driverRole = Role::where('name', 'driver')->firstOrFail();

        $this->actingAs($admin)->post('/users', [
            'name' => 'New Driver',
            'email' => 'new-driver@example.test',
            'role_id' => $driverRole->id,
            'password' => 'Strong-Temporary-Password-123',
            'password_confirmation' => 'Strong-Temporary-Password-123',
        ])->assertRedirect('/users');

        $created = User::where('email', 'new-driver@example.test')->firstOrFail();
        $this->assertNotSame('Strong-Temporary-Password-123', $created->password);
        $this->assertTrue(Hash::check('Strong-Temporary-Password-123', $created->password));
        $this->assertDatabaseHas('audit_logs', [
            'user_id' => $admin->id,
            'action' => 'user.created',
            'subject_id' => $created->id,
        ]);

        $manager = $this->userWithRole('general_manager');
        $this->actingAs($manager)->get('/users')->assertForbidden();
    }

    public function test_driver_reports_are_limited_to_their_own_records_and_assigned_vehicle()
    {
        $driver = $this->userWithRole('driver');
        $otherDriver = $this->userWithRole('driver', 'other-report@example.test');
        $vehicle = Vehicle::create(['vehicle_number' => 'TR-006']);
        $otherVehicle = Vehicle::create(['vehicle_number' => 'TR-007']);
        DriverAssignment::create([
            'vehicle_id' => $vehicle->id,
            'driver_user_id' => $driver->id,
            'assigned_at' => '2026-11-24',
        ]);
        DriverAssignment::create([
            'vehicle_id' => $otherVehicle->id,
            'driver_user_id' => $otherDriver->id,
            'assigned_at' => '2026-11-24',
        ]);
        $otherReport = VehicleReport::create([
            'user_id' => $otherDriver->id,
            'vehicle_id' => $otherVehicle->id,
            'type' => 'defect',
            'description' => 'Brake warning light is on',
        ]);

        $this->actingAs($driver)->get('/vehicle-reports')->assertOk()->assertDontSee('Brake warning light is on');
        $this->actingAs($driver)->get('/vehicle-reports/'.$otherReport->id)->assertNotFound();
        $this->assertDatabaseHas('audit_logs', [
            'user_id' => $driver->id,
            'action' => 'authorization.denied',
            'subject_id' => $otherReport->id,
        ]);
        $this->actingAs($driver)->post('/vehicle-reports', [
            'vehicle_id' => $otherVehicle->id,
            'type' => 'defect',
            'description' => 'Tampered vehicle ID report',
        ])->assertNotFound();

        $this->actingAs($driver)->post('/vehicle-reports', [
            'vehicle_id' => $vehicle->id,
            'type' => 'condition',
            'description' => 'Vehicle condition is normal',
        ])->assertRedirect();
        $this->assertDatabaseHas('vehicle_reports', [
            'user_id' => $driver->id,
            'vehicle_id' => $vehicle->id,
            'type' => 'condition',
        ]);
    }

    private function userWithRole(string $roleName, string $email = null): User
    {
        return User::create([
            'name' => ucfirst(str_replace('_', ' ', $roleName)),
            'email' => $email ?: $roleName.'@example.test',
            'password' => Hash::make('Correct-Horse-Battery-1'),
            'role_id' => Role::where('name', $roleName)->value('id'),
            'is_active' => true,
        ]);
    }

    private function trip(Vehicle $vehicle, User $driver): Trip
    {
        return Trip::create([
            'vehicle_id' => $vehicle->id,
            'driver_user_id' => $driver->id,
            'origin' => 'Start',
            'destination' => 'End',
        ]);
    }
}
