<?php

namespace App\Console\Commands;

use App\Models\AuditLog;
use App\Models\Role;
use App\Models\User;
use Illuminate\Console\Command;
use Illuminate\Support\Facades\Hash;

class BootstrapAdmin extends Command
{
    protected $signature = 'rbac:bootstrap-admin {email? : Email of an existing account to promote}';

    protected $description = 'Create or promote the initial IT Manager account';

    public function handle()
    {
        $adminRole = Role::where('name', 'it_manager')->first();
        if (!$adminRole) {
            $this->error('RBAC roles are not seeded. Run php artisan db:seed first.');
            return 1;
        }

        if (User::where('role_id', $adminRole->id)->where('is_active', true)->exists()) {
            $this->error('An active IT Manager already exists.');
            return 1;
        }

        $email = $this->argument('email');
        $user = $email ? User::where('email', $email)->first() : null;
        $requestedRole = null;

        if ($email && !$user) {
            $this->error('No account exists with that email address. Omit the email to create the initial account.');
            return 1;
        }

        if (!$user) {
            $name = $this->ask('Administrator name');
            $email = $this->ask('Administrator email');
            $password = $this->secret('Password (minimum 12 characters)');
            $confirmation = $this->secret('Confirm password');

            if (!$name || !filter_var($email, FILTER_VALIDATE_EMAIL) || User::where('email', $email)->exists()) {
                $this->error('A name and unique, valid email address are required.');
                return 1;
            }
            if (strlen($password) < 12 || $password !== $confirmation) {
                $this->error('The password must be at least 12 characters and match its confirmation.');
                return 1;
            }

            $user = User::create([
                'name' => $name,
                'email' => $email,
                'password' => Hash::make($password),
                'role_id' => $adminRole->id,
                'is_active' => true,
            ]);
        } else {
            if (!$this->confirm('Assign '.$user->email.' as the initial IT Manager and activate the account?')) {
                $this->info('No account was changed.');
                return 0;
            }

            $requestedRole = $user->requestedRole->name ?? null;
            $user->role_id = $adminRole->id;
            $user->requested_role_id = null;
            $user->registration_status = 'approved';
            $user->is_active = true;
            $user->save();
        }

        AuditLog::create([
            'user_id' => $user->id,
            'action' => 'user.initial_admin_assigned',
            'subject_type' => User::class,
            'subject_id' => $user->id,
            'details' => [
                'role' => $adminRole->name,
                'requested_role' => $requestedRole ?? null,
            ],
        ]);

        $this->info('Initial IT Manager role assigned to '.$user->email.'.');
        return 0;
    }
}
