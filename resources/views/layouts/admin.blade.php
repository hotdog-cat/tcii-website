<!doctype html>
<html lang="en">
<head>
    <meta charset="utf-8"><meta name="viewport" content="width=device-width, initial-scale=1">
    <title>@yield('title', 'Admin') | Techtonic CMS</title>
    <link rel="stylesheet" href="{{ asset('css/admin.css') }}">
</head>
<body>
<div class="admin-shell">
    <aside class="sidebar">
        <a class="admin-brand" href="{{ route('admin.dashboard') }}"><img src="{{ asset('images/techtonic-logo-white.png') }}" alt="Techtonic"><span>Content Management</span></a>
        <nav>
            <a class="{{ request()->routeIs('admin.dashboard') ? 'active' : '' }}" href="{{ route('admin.dashboard') }}">Dashboard</a>
            @foreach(\App\Models\ContentItem::TYPES as $key => $label)
                <a class="{{ request('type') === $key ? 'active' : '' }}" href="{{ route('admin.items.index', ['type' => $key]) }}">{{ $label }}</a>
            @endforeach
            <a class="{{ request()->routeIs('admin.messages.*') ? 'active' : '' }}" href="{{ route('admin.messages.index') }}">Contact Messages</a>
        </nav>
        <div class="sidebar-bottom"><a href="{{ route('home') }}" target="_blank">View Website ↗</a><form action="{{ route('admin.logout') }}" method="post">@csrf<button type="submit">Sign Out</button></form></div>
    </aside>
    <div class="admin-main">
        <header><button class="sidebar-toggle" type="button" aria-label="Toggle menu">☰</button><div><span>Signed in as</span><strong>{{ auth()->user()->name }}</strong></div></header>
        <main class="admin-content">
            @if(session('success'))<div class="alert success">{{ session('success') }}</div>@endif
            @if($errors->any())<div class="alert error"><strong>Please correct the following:</strong><ul>@foreach($errors->all() as $error)<li>{{ $error }}</li>@endforeach</ul></div>@endif
            @yield('content')
        </main>
    </div>
</div>
<script>document.querySelector('.sidebar-toggle')?.addEventListener('click',()=>document.body.classList.toggle('sidebar-open'));</script>
</body>
</html>
