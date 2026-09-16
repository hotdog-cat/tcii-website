<!doctype html>
<html lang="en"><head><meta charset="utf-8"><meta name="viewport" content="width=device-width, initial-scale=1"><title>Admin Login | Techtonic</title><link rel="icon" type="image/png" href="{{ asset('favicon.png') }}?v=2"><link rel="stylesheet" href="{{ asset('css/admin.css') }}"></head>
<body class="login-page">
<main class="login-card">
    <img src="{{ asset('images/techtonic-logo.png') }}" alt="Techtonic Concrete Industries Inc.">
    <p class="login-label">Website Administration</p><h1>Welcome back.</h1><p>Sign in to manage website content and inquiries.</p>
    @if($errors->any())<div class="alert error">{{ $errors->first() }}</div>@endif
    <form method="post" action="{{ route('login.store') }}">@csrf
        <label>Email address<input type="email" name="email" value="{{ old('email') }}" autocomplete="email" required autofocus></label>
        <label>Password<input type="password" name="password" autocomplete="current-password" required></label>
        <label class="check"><input type="checkbox" name="remember" value="1"> Remember me</label>
        <button class="primary-btn" type="submit">Sign In</button>
    </form>
    <a class="back-link" href="{{ route('home') }}">← Return to website</a>
</main>
</body></html>
