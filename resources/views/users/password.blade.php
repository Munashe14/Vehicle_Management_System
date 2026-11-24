<x-app-layout title="Reset Password · Fleet Manager">
    <div class="page-header"><div><span class="page-eyebrow">Administration</span><h1 class="page-title">Reset {{ $user->name }}'s password</h1></div></div>
    @if ($errors->any())<div class="alert alert-error">{{ $errors->first() }}</div>@endif
    <div class="form-panel"><form method="POST" action="/users/{{ $user->id }}/password">@csrf @method('PUT')
        <div class="field"><label>New password (minimum 12 characters)</label><input type="password" name="password" required autocomplete="new-password"></div>
        <div class="field"><label>Confirm password</label><input type="password" name="password_confirmation" required autocomplete="new-password"></div>
        <div class="form-actions"><button class="btn btn-primary">Reset password</button><a class="btn btn-ghost" href="/users">Cancel</a></div>
    </form></div>
</x-app-layout>
