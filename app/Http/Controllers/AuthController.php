<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\RateLimiter;
use Illuminate\Support\Str;
use App\Models\User;
use App\Models\Role;
use App\Models\AuditLog;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;
use Illuminate\Validation\Rule;

class AuthController extends Controller
{
    public function showLogin()
    {
        return view('auth.login');
    }

    public function showRegistration()
    {
        $roles = Role::whereIn('name', [
            'it_manager',
            'fleet_supervisor',
            'driver',
            'finance_manager',
            'general_manager',
        ])->orderBy('label')->get();

        return view('auth.register', compact('roles'));
    }

    public function register(Request $request)
    {
        $licenseNumber = trim((string) $request->input('license_number', ''));
        $request->merge([
            'email' => Str::lower(trim($request->input('email', ''))),
            'license_number' => $licenseNumber === '' ? null : $licenseNumber,
        ]);

        $data = $request->validate([
            'name' => 'required|string|max:255',
            'email' => 'required|email|max:255|unique:users,email',
            'phone' => 'nullable|string|max:50',
            'license_number' => 'nullable|string|max:100|unique:users,license_number',
            'requested_role_id' => [
                'required',
                'integer',
                Rule::in(Role::whereIn('name', [
                    'it_manager',
                    'fleet_supervisor',
                    'driver',
                    'finance_manager',
                    'general_manager',
                ])->pluck('id')->all()),
            ],
            'password' => 'required|string|min:12|max:72|confirmed',
        ]);

        $requestedRole = Role::findOrFail($data['requested_role_id']);
        DB::transaction(function () use ($data, $requestedRole, $request) {
            $user = User::create([
                'name' => $data['name'],
                'email' => $data['email'],
                'phone' => $data['phone'] ?? null,
                'license_number' => $data['license_number'] ?? null,
                'password' => Hash::make($data['password']),
                'requested_role_id' => $requestedRole->id,
                'registration_status' => 'pending',
                'is_active' => false,
            ]);

            AuditLog::create([
                'user_id' => $user->id,
                'action' => 'user.registration_requested',
                'subject_type' => User::class,
                'subject_id' => $user->id,
                'details' => ['requested_role' => $requestedRole->name],
                'ip_address' => $request->ip(),
            ]);

        });

        return redirect()->route('login')->with(
            'success',
            'Your registration request has been submitted. You can sign in after an IT Manager approves your account and requested role.'
        );
    }

    public function login(Request $request)
    {
        $request->merge([
            'email' => Str::lower(trim($request->input('email', ''))),
        ]);

        $request->validate([
            'email' => 'required|email',
            'password' => 'required|string',
        ]);

        $key = Str::lower($request->input('email')).'|'.$request->ip();
        if (RateLimiter::tooManyAttempts($key, 5)) {
            return back()->withErrors([
                'email' => 'Too many login attempts. Please try again in '.RateLimiter::availableIn($key).' seconds.',
            ])->onlyInput('email');
        }

        $account = User::where('email', $request->input('email'))->first();

        if ($account && Hash::check($request->input('password'), $account->password)) {
            if ($account->registration_status === 'pending') {
                RateLimiter::clear($key);
                return back()->withErrors([
                    'email' => 'Your registration request is awaiting IT Manager approval.',
                ])->onlyInput('email');
            }

            if ($account->registration_status === 'rejected') {
                RateLimiter::clear($key);
                return back()->withErrors([
                    'email' => 'Your registration request was not approved. Contact the IT Manager for assistance.',
                ])->onlyInput('email');
            }

            if (!$account->is_active || !$account->role_id) {
                RateLimiter::clear($key);
                return back()->withErrors([
                    'email' => 'Your account needs administrator setup before you can sign in. Contact the IT Manager.',
                ])->onlyInput('email');
            }
        }

        if (!$account || !Auth::attempt([
            'email' => $request->input('email'),
            'password' => $request->input('password'),
            'is_active' => true,
        ], $request->boolean('remember'))) {
            RateLimiter::hit($key, 60);
            return back()->withErrors(['email' => 'The supplied credentials are invalid.'])->onlyInput('email');
        }

        RateLimiter::clear($key);
        $request->session()->regenerate();

        return redirect()->route('home');
    }

    public function logout(Request $request)
    {
        Auth::logout();
        $request->session()->invalidate();
        $request->session()->regenerateToken();

        return redirect()->route('login');
    }
}
