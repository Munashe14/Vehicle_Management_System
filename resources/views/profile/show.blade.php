<x-app-layout title="My Profile · Fleet Manager">
    <div class="page-header"><div><span class="page-eyebrow">My account</span><h1 class="page-title">{{ $user->name }}</h1></div></div>
    <div class="form-panel">
        <p><strong>Email:</strong> {{ $user->email }}</p>
        <p><strong>Role:</strong> {{ $user->role->label }}</p>
        @if ($user->phone)<p><strong>Phone:</strong> {{ $user->phone }}</p>@endif
        @if ($user->license_number)<p><strong>License:</strong> {{ $user->license_number }}</p>@endif
    </div>
</x-app-layout>
