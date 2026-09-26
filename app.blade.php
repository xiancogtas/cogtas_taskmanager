<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <title>@yield('title', 'Orbit Desk') - Orbit Desk</title>
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=DM+Mono:wght@400;500&family=DM+Sans:wght@400;500;600;700&family=Space+Grotesk:wght@500;600;700&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="/css/app.css?v={{ filemtime(public_path('css/app.css')) }}">
</head>
<body>
    <div class="app-shell">
        <aside class="sidebar">
            <a class="brand" href="{{ route('tasks.index') }}" aria-label="Orbit Desk dashboard">
                <span class="brand-mark" aria-hidden="true">
                    <svg viewBox="0 0 40 40" fill="none"><circle cx="20" cy="20" r="13" stroke="currentColor" stroke-width="1.5"/><ellipse cx="20" cy="20" rx="18" ry="7" transform="rotate(-32 20 20)" stroke="currentColor" stroke-width="1.5"/><circle cx="30" cy="13" r="3" fill="currentColor"/></svg>
                </span>
                <span><strong>orbit</strong><small>PERSONAL SPACE</small></span>
            </a>

            <div class="sidebar-label">WORKSPACE</div>
            <nav class="side-nav" aria-label="Main navigation">
                <a class="nav-link {{ request()->routeIs('tasks.index') ? 'is-active' : '' }}" href="{{ route('tasks.index') }}">
                    <svg viewBox="0 0 20 20" aria-hidden="true"><rect x="3" y="3" width="5" height="5" rx="1"/><rect x="12" y="3" width="5" height="5" rx="1"/><rect x="3" y="12" width="5" height="5" rx="1"/><rect x="12" y="12" width="5" height="5" rx="1"/></svg>
                    <span>Overview</span>
                </a>
                <a class="nav-link {{ request()->routeIs('tasks.create') ? 'is-active' : '' }}" href="{{ route('tasks.create') }}">
                    <svg viewBox="0 0 20 20" aria-hidden="true"><path d="M10 4v12M4 10h12"/></svg>
                    <span>New task</span>
                </a>
            </nav>

            <div class="sidebar-label section-label">STATUS</div>
            <nav class="side-nav" aria-label="Task status filters">
                <a class="nav-link" href="{{ route('tasks.index', ['status' => 'pending']) }}"><span class="status-dot pending-dot"></span><span>In progress</span></a>
                <a class="nav-link" href="{{ route('tasks.index', ['status' => 'completed']) }}"><span class="status-dot complete-dot"></span><span>Completed</span></a>
            </nav>

            <div class="sidebar-bottom">
                <div class="orbit-note"><span class="orbit-note-icon">01</span><div><strong>Your own orbit</strong><span>Small steps, far horizons.</span></div></div>
                <div class="sidebar-foot"><span class="online-dot"></span> LOCAL WORKSPACE</div>
            </div>
        </aside>

        <div class="main-shell">
            <header class="topbar">
                <div class="breadcrumb"><span>MY SPACE</span><i>/</i><strong>@yield('eyebrow', 'OVERVIEW')</strong></div>
                <div class="topbar-right"><span class="today-label">{{ now()->format('D, M j') }}</span><span class="avatar">Y</span></div>
            </header>

            <main class="page-content">
                @if (session('success'))
                    <div class="flash-message" role="status">{{ session('success') }}</div>
                @endif
                @yield('content')
            </main>
        </div>
    </div>
</body>
</html>