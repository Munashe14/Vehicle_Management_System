<x-app-layout title="Edit User · Fleet Manager">
    <div class="page-header"><div><span class="page-eyebrow">Administration</span><h1 class="page-title">Edit {{ $user->name }}</h1></div></div>
    @if ($errors->any())<div class="alert alert-error">{{ $errors->first() }}</div>@endif
    <div class="form-panel"><form method="POST" action="/users/{{ $user->id }}">@csrf @method('PUT')
        <div class="field"><label>Name</label><input name="name" value="{{ old('name', $user->name) }}" required></div>
        <div class="field"><label>Email</label><input type="email" name="email" value="{{ old('email', $user->email) }}" required></div>
        <div class="field"><label>Role</label><select name="role_id" required>@foreach ($roles as $role)<option value="{{ $role->id }}" @selected(old('role_id', $user->role_id) == $role->id)>{{ $role->label }}</option>@endforeach</select></div>
        <div class="form-actions"><button class="btn btn-primary">Save changes</button><a class="btn btn-ghost" href="/users">Cancel</a></div>
    </form></div>
</x-app-layout>
