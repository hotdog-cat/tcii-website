<!doctype html>
<html lang="en">
<head>
    <meta charset="utf-8"><meta name="viewport" content="width=device-width, initial-scale=1">
    <title>@yield('title', 'Admin') | Techtonic CMS</title>
    <link rel="icon" type="image/png" href="{{ asset('favicon.png') }}?v=2">
    <link rel="stylesheet" href="{{ asset('css/admin.css') }}">
</head>
<body>
<div class="admin-shell">
    <aside class="sidebar">
        <a class="admin-brand" href="{{ route('admin.dashboard') }}"><img src="{{ asset('images/techtonic-logo-white.png') }}" alt="Techtonic"><span>Content Management</span></a>
        <nav>
            <a class="{{ request()->routeIs('admin.dashboard') ? 'active' : '' }}" href="{{ route('admin.dashboard') }}">Dashboard</a>
            <a class="{{ request()->routeIs('admin.page-content.*') ? 'active' : '' }}" href="{{ route('admin.page-content.edit') }}">Page Content</a>
            @foreach(\App\Models\ContentItem::TYPES as $key => $label)
                <a class="{{ request('type') === $key ? 'active' : '' }}" href="{{ route('admin.items.index', ['type' => $key]) }}">{{ $label }}</a>
            @endforeach
            <a class="{{ request()->routeIs('admin.messages.*') ? 'active' : '' }}" href="{{ route('admin.messages.index') }}">Contact Messages</a>
            @if(auth()->user()?->isAdmin())
                <a class="{{ request()->routeIs('admin.users.*') ? 'active' : '' }}" href="{{ route('admin.users.index') }}">User Management</a>
                <a class="{{ request()->routeIs('admin.activity-log.*') ? 'active' : '' }}" href="{{ route('admin.activity-log.index') }}">Activity Log</a>
            @endif
        </nav>
        <div class="sidebar-bottom"><a href="{{ route('home') }}" target="_blank">View Website &rarr;</a></div>
    </aside>
    <div class="admin-main">
        <header>
            <button class="sidebar-toggle" type="button" aria-label="Toggle menu">&#9776;</button>
            <div class="account-menu" data-open="false">
                <button class="account-trigger" type="button" aria-haspopup="true" aria-expanded="false">
                    <span class="account-copy"><span>Signed in as</span><strong>{{ auth()->user()->name }}</strong></span>
                    @if(auth()->user()->profile_photo_url)
                        <img src="{{ auth()->user()->profile_photo_url }}" alt="{{ auth()->user()->name }}">
                    @else
                        <span class="account-avatar">{{ strtoupper(substr(auth()->user()->name, 0, 1)) }}</span>
                    @endif
                </button>
                <div class="account-dropdown" hidden>
                    <form action="{{ route('admin.logout') }}" method="post">@csrf<button type="submit">Sign Out</button></form>
                </div>
            </div>
        </header>
        <main class="admin-content">
            @if(session('success'))<div class="alert success">{{ session('success') }}</div>@endif
            @if($errors->any())<div class="alert error"><strong>Please correct the following:</strong><ul>@foreach($errors->all() as $error)<li>{{ $error }}</li>@endforeach</ul></div>@endif
            @yield('content')
        </main>
    </div>
</div>
<script>
document.querySelector('.sidebar-toggle')?.addEventListener('click',()=>document.body.classList.toggle('sidebar-open'));
(() => {
    const accountMenu = document.querySelector('.account-menu');
    const accountTrigger = document.querySelector('.account-trigger');
    const accountDropdown = document.querySelector('.account-dropdown');

    if (!accountMenu || !accountTrigger || !accountDropdown) return;

    const setAccountMenu = (isOpen) => {
        accountMenu.dataset.open = String(isOpen);
        accountTrigger.setAttribute('aria-expanded', String(isOpen));
        accountDropdown.hidden = !isOpen;
    };

    accountTrigger.addEventListener('click', (event) => {
        event.preventDefault();
        event.stopPropagation();
        setAccountMenu(accountMenu.dataset.open !== 'true');
    });

    document.addEventListener('click', (event) => {
        if (!accountMenu.contains(event.target)) setAccountMenu(false);
    });

    document.addEventListener('keydown', (event) => {
        if (event.key === 'Escape') setAccountMenu(false);
    });
})();
</script>
</body>
</html>
