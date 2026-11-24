<x-app-layout title="User Accounts · Fleet Manager">
    <div class="page-header">
        <div><span class="page-eyebrow">Administration</span><h1 class="page-title">User accounts</h1></div>
        <a class="btn btn-primary" href="/users/create">Add user</a>
    </div>
    @if (session('success'))<div class="alert alert-success">{{ session('success') }}</div>@endif
    @if ($errors->any())<div class="alert alert-error">{{ $errors->first() }}</div>@endif
    @if ($pendingCount > 0)
        <div class="alert alert-success">{{ $pendingCount }} registration {{ $pendingCount === 1 ? 'request is' : 'requests are' }} waiting for review.</div>
    @endif
    <div class="table-wrap"><table><thead><tr><th>Name</th><th>Email</th><th>Role</th><th>Status</th><th>Actions</th></tr></thead><tbody>
        @foreach ($users as $user)
            <tr>
                <td>{{ $user->name }}</td><td class="table-cell-wrap">{{ $user->email }}</td>
                <td>{{ $user->role->label ?? ($user->requestedRole->label ?? 'Unassigned') }}</td>
                <td>
                    @if ($user->registration_status === 'pending')Pending approval
                    @elseif ($user->registration_status === 'rejected')Request rejected
                    @elseif ($user->is_active)Active
                    @elseif (!$user->role_id && $user->requested_role_id)Needs role approval
                    @else Deactivated
                    @endif
                </td>
                <td class="table-cell-actions">
                    @if (in_array($user->registration_status, ['pending', 'approved'], true) && !$user->is_active && !$user->role_id && $user->requested_role_id)
                        @if (auth()->user()->hasPermission('users.approve'))
                            <div class="table-actions">
                                <form method="POST" action="/users/{{ $user->id }}/approve">@csrf @method('PATCH')<button class="btn btn-primary" type="submit">Approve {{ $user->requestedRole->label ?? 'request' }}</button></form>
                                <form method="POST" action="/users/{{ $user->id }}/reject">@csrf @method('PATCH')<button class="btn btn-danger" type="submit">Reject</button></form>
                            </div>
                        @endif
                    @else
                        <div class="table-actions">
                            <a class="btn btn-ghost" href="/users/{{ $user->id }}/edit">Edit</a>
                            <a class="btn btn-ghost" href="/users/{{ $user->id }}/password">Reset password</a>
                            @if ($user->is_active && $user->id !== auth()->id())
                                <form method="POST" action="/users/{{ $user->id }}/deactivate">@csrf @method('PATCH')<button class="btn btn-ghost" type="submit">Deactivate</button></form>
                            @endif
                        </div>
                    @endif
                </td>
            </tr>
        @endforeach
    </tbody></table></div>
    <div class="pagination-wrap">{{ $users->links() }}</div>
</x-app-layout>
