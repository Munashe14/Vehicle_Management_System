<x-app-layout title="User Accounts · Fleet Manager">
    <div class="page-header">
        <div><span class="page-eyebrow">Administration</span><h1 class="page-title">User accounts</h1></div>
        <a class="btn btn-primary" href="/users/create">Add user</a>
    </div>
    @if (session('success'))<div class="alert alert-success">{{ session('success') }}</div>@endif
    @if ($errors->any())<div class="alert alert-error">{{ $errors->first() }}</div>@endif
    <div class="table-wrap"><table><thead><tr><th>Name</th><th>Email</th><th>Role</th><th>Status</th><th>Actions</th></tr></thead><tbody>
        @foreach ($users as $user)
            <tr>
                <td>{{ $user->name }}</td><td class="table-cell-wrap">{{ $user->email }}</td>
                <td>{{ $user->role->label ?? 'Unassigned' }}</td>
                <td>{{ $user->is_active && $user->role_id ? 'Active' : 'Deactivated' }}</td>
                <td class="table-cell-actions">
                    <div class="table-actions">
                        <a class="btn btn-ghost" href="/users/{{ $user->id }}/edit">Edit</a>
                        <a class="btn btn-ghost" href="/users/{{ $user->id }}/password">Reset password</a>
                        @if ($user->is_active && $user->id !== auth()->id())
                            <form method="POST" action="/users/{{ $user->id }}/deactivate">@csrf @method('PATCH')<button class="btn btn-ghost" type="submit">Deactivate</button></form>
                        @endif
                    </div>
                </td>
            </tr>
        @endforeach
    </tbody></table></div>
    <div class="pagination-wrap">{{ $users->links() }}</div>
</x-app-layout>
