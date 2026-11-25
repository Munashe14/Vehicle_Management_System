<?php

namespace App\Http\Controllers;

use App\Models\AuditLog;
use App\Models\Role;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Str;

class InitialAdminController extends Controller
{
    public function show()
    {
        abort_unless($this->setupIsAvailable(), 404);

        return view('auth.initial-admin');
    }

    public function store(Request $request)
    {
        abort_unless($this->setupIsAvailable(), 404);

        $request->merge([
            'email' => Str::lower(trim($request->input('email', ''))),
        ]);

        $data = $request->validate([
            'setup_key' => 'required|string|max:255',
            'name' => 'required|string|max:255',
            'email' => 'required|email|max:255',
            'password' => 'required|string|min:12|max:72|confirmed',
        ]);

        $setupKey = (string) config('app.initial_admin_setup_key');
        if ($setupKey === '' || !hash_equals($setupKey, $data['setup_key'])) {
            return back()->withErrors([
                'setup_key' => 'The setup code is invalid.',
            ]);
        }

        DB::transaction(function () use ($data, $request) {
            $adminRole = Role::where('name', 'it_manager')->lockForUpdate()->firstOrFail();
            abort_unless($this->setupIsAvailable(), 404);

            $user = User::where('email', $data['email'])->lockForUpdate()->first();
            $requestedRole = null;
            if ($user) {
                abort_unless(
                    !$user->is_active
                        && !$user->role_id
                        && in_array($user->registration_status, ['pending', 'approved'], true),
                    422,
                    'That email is already attached to an account that cannot be used for initial setup.'
                );

                $requestedRole = $user->requestedRole->name ?? null;
                $user->fill([
                    'name' => $data['name'],
                    'password' => Hash::make($data['password']),
                    'role_id' => $adminRole->id,
                    'requested_role_id' => null,
                    'registration_status' => 'approved',
                    'is_active' => true,
                ])->save();
            } else {
                $user = User::create([
                    'name' => $data['name'],
                    'email' => $data['email'],
                    'password' => Hash::make($data['password']),
                    'role_id' => $adminRole->id,
                    'requested_role_id' => null,
                    'registration_status' => 'approved',
                    'is_active' => true,
                ]);
            }

            AuditLog::create([
                'user_id' => $user->id,
                'action' => 'user.initial_admin_assigned',
                'subject_type' => User::class,
                'subject_id' => $user->id,
                'details' => [
                    'role' => $adminRole->name,
                    'requested_role' => $requestedRole,
                    'setup_method' => 'web_setup',
                ],
                'ip_address' => $request->ip(),
            ]);
        });

        return redirect()->route('login')->with(
            'success',
            'Initial IT Manager account created. You can now sign in.'
        );
    }

    private function setupIsAvailable(): bool
    {
        $setupKey = config('app.initial_admin_setup_key');
        if (!is_string($setupKey) || $setupKey === '') {
            return false;
        }

        $adminRoleId = Role::where('name', 'it_manager')->value('id');
        if (!$adminRoleId
            || User::where('role_id', $adminRoleId)->where('is_active', true)->exists()
            || AuditLog::where('action', 'user.initial_admin_assigned')->exists()) {
            return false;
        }

        return true;
    }
}
