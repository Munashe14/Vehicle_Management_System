<x-app-layout title="Add User · Fleet Manager">
    <div class="page-header"><div><span class="page-eyebrow">Administration</span><h1 class="page-title">Add user</h1></div></div>
    @if ($errors->any())<div class="alert alert-error"><ul>@foreach ($errors->all() as $error)<li>{{ $error }}</li>@endforeach</ul></div>@endif
    <div class="form-panel">
        <p class="empty-state-body" style="margin:0 0 20px;max-width:none;text-align:left">Choose the role for this account. The user signs in with their own email and password and receives only that role’s permissions.</p>
        <form method="POST" action="/users">@csrf
        <div class="field"><label>Name</label><input name="name" value="{{ old('name') }}" required></div>
        <div class="field"><label>Email</label><input type="email" name="email" value="{{ old('email') }}" required></div>
        <div class="field"><label>Role</label><select name="role_id" required>@foreach ($roles as $role)<option value="{{ $role->id }}" @selected(old('role_id') == $role->id)>{{ $role->label }}</option>@endforeach</select></div>
        <div class="field"><label>Temporary password (minimum 12 characters)</label><input type="password" name="password" required autocomplete="new-password"></div>
        <div class="field"><label>Confirm password</label><input type="password" name="password_confirmation" required autocomplete="new-password"></div>
        <div class="form-actions"><button class="btn btn-primary">Create account</button><a class="btn btn-ghost" href="/users">Cancel</a></div>
        </form>
    </div>
</x-app-layout>
