<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Create account · Fleet Manager</title>
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
            <span class="auth-kicker">YOUR FLEET. YOUR WORKSPACE.</span>
            <h1>Join the team<br>that keeps things<br><span>moving forward.</span></h1>
            <p>Request access to the fleet workspace that matches your responsibilities.</p>
            <div class="auth-visual" aria-hidden="true">
                <div class="auth-visual-line"></div>
                <div class="auth-vehicle">
                    <span class="auth-vehicle-window"></span>
                    <span class="auth-vehicle-cab"></span>
                    <span class="auth-wheel auth-wheel-one"></span>
                    <span class="auth-wheel auth-wheel-two"></span>
                </div>
                <span class="auth-visual-label">ACCOUNT ACCESS <b>● APPROVAL REQUIRED</b></span>
            </div>
        </div>
        <span class="auth-aside-foot">Built for the people behind every delivery.</span>
    </aside>

    <main class="auth-main">
        <section class="auth-card auth-card-register" aria-labelledby="auth-title">
            <div class="auth-card-heading">
                <span class="auth-card-icon" aria-hidden="true">＋</span>
                <span class="auth-eyebrow">GET STARTED</span>
                <h2 id="auth-title">Create your account</h2>
                <p>Choose your role request. An IT Manager must approve it before you can sign in.</p>
            </div>

            @if ($errors->any())
                <div class="alert alert-error" role="alert">
                    <ul>@foreach ($errors->all() as $error)<li>{{ $error }}</li>@endforeach</ul>
                </div>
            @endif

            <form method="POST" action="{{ route('register.submit') }}" class="auth-form">
                @csrf
                <div class="auth-field">
                    <label for="name">Full name</label>
                    <input id="name" type="text" name="name" value="{{ old('name') }}" placeholder="e.g. Daniel Moyo" required autocomplete="name" maxlength="255">
                </div>
                <div class="auth-field">
                    <label for="email">Work email</label>
                    <input id="email" type="email" name="email" value="{{ old('email') }}" placeholder="you@company.com" required autocomplete="email" maxlength="255">
                </div>
                <div class="auth-field">
                    <label for="requested_role_id">Role you are requesting</label>
                    <select id="requested_role_id" name="requested_role_id" required>
                        <option value="">Select a role</option>
                        @foreach ($roles as $role)
                            <option value="{{ $role->id }}" {{ old('requested_role_id') == $role->id ? 'selected' : '' }}>{{ $role->label }}</option>
                        @endforeach
                    </select>
                </div>
                <div class="auth-field-row">
                    <div class="auth-field">
                        <label for="phone">Phone <span class="auth-optional">OPTIONAL</span></label>
                        <input id="phone" type="tel" name="phone" value="{{ old('phone') }}" placeholder="+263 77 000 0000" autocomplete="tel" maxlength="50">
                    </div>
                    <div class="auth-field">
                        <label for="license_number">License no. <span class="auth-optional">OPTIONAL</span></label>
                        <input id="license_number" type="text" name="license_number" value="{{ old('license_number') }}" placeholder="Driver's license" maxlength="100">
                    </div>
                </div>
                <div class="auth-field">
                    <label for="password">Password <span class="auth-optional">12+ CHARACTERS</span></label>
                    <input id="password" type="password" name="password" placeholder="Create a strong password" required autocomplete="new-password" minlength="12" maxlength="72">
                </div>
                <div class="auth-field">
                    <label for="password_confirmation">Confirm password</label>
                    <input id="password_confirmation" type="password" name="password_confirmation" placeholder="Enter your password again" required autocomplete="new-password" minlength="12" maxlength="72">
                </div>
                <div class="auth-role-note"><span aria-hidden="true">✓</span><span><strong>Access is reviewed</strong><small>Your account stays inactive until an IT Manager verifies and approves your requested role.</small></span></div>
                <button class="auth-submit" type="submit">Submit registration request <span aria-hidden="true">→</span></button>
            </form>

            <div class="auth-card-footer">
                <span>Already have an approved account?</span>
                <a href="{{ route('login') }}">Sign in</a>
            </div>
            <p class="auth-security-note"><span aria-hidden="true">◇</span> Your password is stored securely and never shared.</p>
        </section>
        <footer class="auth-page-footer">© {{ date('Y') }} Trident Recycling <span>·</span> Fleet Management System</footer>
    </main>
</body>
</html>
