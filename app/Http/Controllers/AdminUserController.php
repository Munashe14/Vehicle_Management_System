<?php

namespace App\Http\Controllers;

use App\Models\AuditLog;
use App\Models\Role;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;

class AdminUserController extends Controller
{
    public function __construct()
    {
        $this->middleware('permission:users.view')->only('index');
        $this->middleware('permission:users.create')->only(['create', 'store']);
        $this->middleware('permission:users.update')->only(['edit', 'update', 'editPassword', 'updatePassword']);
        $this->middleware('permission:users.deactivate')->only('deactivate');
    }

    public function index()
    {
        $users = User::with('role')->orderBy('name')->paginate(25);
        return view('users.index', compact('users'));
    }

    public function create()
    {
        $roles = Role::orderBy('label')->get();
        return view('users.create', compact('roles'));
    }

    public function store(Request $request)
    {
        $data = $request->validate([
            'name' => 'required|string|max:255',
            'email' => 'required|email|max:255|unique:users,email',
            'role_id' => 'required|exists:roles,id',
            'password' => 'required|string|min:12|confirmed',
        ]);

        $user = DB::transaction(function () use ($data) {
            $user = User::create([
                'name' => $data['name'],
                'email' => $data['email'],
                'role_id' => $data['role_id'],
                'password' => Hash::make($data['password']),
                'is_active' => true,
            ]);

            $this->recordAudit('user.created', $user, ['role_id' => $user->role_id]);
            return $user;
        });

        return redirect('/users')->with('success', 'User account created.');
    }

    public function edit(User $user)
    {
        $roles = Role::orderBy('label')->get();
        return view('users.edit', compact('user', 'roles'));
    }

    public function update(Request $request, User $user)
    {
        $data = $request->validate([
            'name' => 'required|string|max:255',
            'email' => 'required|email|max:255|unique:users,email,'.$user->id,
            'role_id' => 'required|exists:roles,id',
        ]);

        DB::transaction(function () use ($user, $data) {
            $user = User::whereKey($user->id)->lockForUpdate()->firstOrFail();
            $newRole = Role::findOrFail($data['role_id']);
            if ($user->is_active && $user->isRole('it_manager') && $newRole->name !== 'it_manager') {
                $this->ensureAnotherActiveAdminExists($user);
            }

            $user->fill([
                'name' => $data['name'],
                'email' => $data['email'],
                'role_id' => $newRole->id,
            ])->save();
            $this->recordAudit('user.updated', $user, ['role_id' => $newRole->id]);
        });

        return redirect('/users')->with('success', 'User account updated.');
    }

    public function editPassword(User $user)
    {
        return view('users.password', compact('user'));
    }

    public function updatePassword(Request $request, User $user)
    {
        $data = $request->validate([
            'password' => 'required|string|min:12|confirmed',
        ]);

        $user->password = Hash::make($data['password']);
        $user->save();
        $this->recordAudit('user.password_reset', $user);

        return redirect('/users')->with('success', 'Password reset.');
    }

    public function deactivate(User $user)
    {
        if ($user->id === auth()->id()) {
            return back()->withErrors(['user' => 'You cannot deactivate your own account.']);
        }

        DB::transaction(function () use ($user) {
            $user = User::whereKey($user->id)->lockForUpdate()->firstOrFail();
            if ($user->is_active && $user->isRole('it_manager')) {
                $this->ensureAnotherActiveAdminExists($user);
            }

            $user->is_active = false;
            $user->save();
            $this->recordAudit('user.deactivated', $user);
        });

        return redirect('/users')->with('success', 'User account deactivated.');
    }

    private function ensureAnotherActiveAdminExists(User $user): void
    {
        $remaining = User::where('role_id', $user->role_id)
            ->where('is_active', true)
            ->where('id', '<>', $user->id)
            ->exists();

        if (!$remaining) {
            abort(422, 'The last active IT Manager cannot be removed or reassigned.');
        }
    }

    private function recordAudit(string $action, User $subject, array $details = []): void
    {
        AuditLog::create([
            'user_id' => auth()->id(),
            'action' => $action,
            'subject_type' => User::class,
            'subject_id' => $subject->id,
            'details' => $details,
            'ip_address' => request()->ip(),
        ]);
    }
}
