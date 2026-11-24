<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Sign in · Fleet Manager</title>
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
            <span class="auth-kicker">TRIDENT RECYCLING · FLEET OPERATIONS</span>
            <h1>Every vehicle.<br>Every route.<br><span>In good hands.</span></h1>
            <p>Your fleet workspace for vehicles, trips, maintenance and the people who keep you moving.</p>
            <div class="auth-visual" aria-hidden="true">
                <div class="auth-visual-line"></div>
                <div class="auth-vehicle">
                    <span class="auth-vehicle-window"></span>
                    <span class="auth-vehicle-cab"></span>
                    <span class="auth-wheel auth-wheel-one"></span>
                    <span class="auth-wheel auth-wheel-two"></span>
                </div>
                <span class="auth-visual-label">FLEET STATUS <b>● OPERATIONAL</b></span>
            </div>
        </div>
        <span class="auth-aside-foot">A clearer view of your fleet.</span>
    </aside>

    <main class="auth-main">
        <section class="auth-card" aria-labelledby="auth-title">
            <div class="auth-card-heading">
                <span class="auth-card-icon" aria-hidden="true">↗</span>
                <span class="auth-eyebrow">WELCOME BACK</span>
                <h2 id="auth-title">Sign in to your account</h2>
                <p>All team roles use the same sign-in. Your assigned role determines your access.</p>
            </div>

            @if ($errors->any())
                <div class="alert alert-error" role="alert">{{ $errors->first() }}</div>
            @endif
            @if (session('success'))
                <div class="alert alert-success" role="status">{{ session('success') }}</div>
            @endif

            <form method="POST" action="{{ route('login.submit') }}" class="auth-form">
                @csrf
                <div class="auth-field">
                    <label for="email">Email address</label>
                    <input id="email" type="email" name="email" value="{{ old('email') }}" placeholder="you@company.com" required autofocus autocomplete="username">
                </div>
                <div class="auth-field">
                    <div class="auth-label-row"><label for="password">Password</label></div>
                    <input id="password" type="password" name="password" placeholder="Enter your password" required autocomplete="current-password">
                </div>
                <label class="auth-checkbox">
                    <input type="checkbox" name="remember" value="1" {{ old('remember') ? 'checked' : '' }}>
                    <span>Remember me on this device</span>
                </label>
                <button class="auth-submit" type="submit">Sign in <span aria-hidden="true">→</span></button>
            </form>

            <div class="auth-card-footer">
                <span>New to Fleet Manager?</span>
                <a href="{{ route('register') }}">Create an account</a>
            </div>
            <p class="auth-security-note"><span aria-hidden="true">◇</span> Your account is protected with secure sign-in.</p>
        </section>
        <footer class="auth-page-footer">© {{ date('Y') }} Trident Recycling <span>·</span> Fleet Management System</footer>
    </main>
</body>
</html>
