<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Initial administrator setup · Fleet Manager</title>
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Archivo:wght@500;600;700;800&family=IBM+Plex+Sans:wght@400;500;600&family=IBM+Plex+Mono:wght@400;500;600&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="{{ asset('css/app.css') }}">
</head>
<body class="auth-page">
    <aside class="auth-aside">
        <a class="auth-brand" href="{{ route('login') }}">
            <span class="auth-brand-mark">FM</span>
            <span><strong>Fleet Manager</strong><small>Operations Console</small></span>
        </a>
        <div class="auth-aside-content">
            <span class="auth-kicker">SYSTEM INITIALIZATION</span>
            <h1>Set up your<br>fleet system<br><span>securely.</span></h1>
            <p>Create the first administrator account to approve registrations and manage access.</p>
        </div>
        <span class="auth-aside-foot">One-time administrator setup.</span>
    </aside>

    <main class="auth-main">
        <section class="auth-card auth-card-register" aria-labelledby="auth-title">
            <div class="auth-card-heading">
                <span class="auth-eyebrow">FIRST-TIME SETUP</span>
                <h2 id="auth-title">Create IT Manager account</h2>
                <p>This setup is available only with the private deployment setup code and before an administrator has been created. It can activate an existing pending account with the same email.</p>
            </div>

            @if ($errors->any())
                <div class="alert alert-error" role="alert">
                    <ul>@foreach ($errors->all() as $error)<li>{{ $error }}</li>@endforeach</ul>
                </div>
            @endif

            <form method="POST" action="{{ route('initial-admin.submit') }}" class="auth-form">
                @csrf
                <div class="auth-field">
                    <label for="setup_key">Private setup code</label>
                    <input id="setup_key" type="password" name="setup_key" required autocomplete="off">
                </div>
                <div class="auth-field">
                    <label for="name">Full name</label>
                    <input id="name" type="text" name="name" value="{{ old('name') }}" required autocomplete="name" maxlength="255">
                </div>
                <div class="auth-field">
                    <label for="email">Administrator email</label>
                    <input id="email" type="email" name="email" value="{{ old('email') }}" required autocomplete="email" maxlength="255">
                </div>
                <div class="auth-field">
                    <label for="password">Password <span class="auth-optional">12+ CHARACTERS</span></label>
                    <input id="password" type="password" name="password" required autocomplete="new-password" minlength="12" maxlength="72">
                </div>
                <div class="auth-field">
                    <label for="password_confirmation">Confirm password</label>
                    <input id="password_confirmation" type="password" name="password_confirmation" required autocomplete="new-password" minlength="12" maxlength="72">
                </div>
                <button class="auth-submit" type="submit">Create initial administrator <span aria-hidden="true">→</span></button>
            </form>

            <div class="auth-card-footer">
                <a href="{{ route('login') }}">Return to sign in</a>
            </div>
        </section>
        <footer class="auth-page-footer">© {{ date('Y') }} Trident Recycling <span>·</span> Fleet Management System</footer>
    </main>
</body>
</html>
