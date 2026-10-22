<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
    <head>
        <meta charset="utf-8">
        <meta name="viewport" content="width=device-width, initial-scale=1">
        <meta name="csrf-token" content="{{ csrf_token() }}">
        <meta name="theme-color" content="#e11d48">
        <meta name="apple-mobile-web-app-capable" content="yes">
        <meta name="apple-mobile-web-app-status-bar-style" content="default">
        <meta name="apple-mobile-web-app-title" content="SHOPLINK">
        <link rel="manifest" href="{{ route('pwa.manifest') }}">
        <link rel="icon" href="/icons/shoplink-icon-192.svg" type="image/svg+xml">
        <link rel="apple-touch-icon" href="/icons/shoplink-icon-192.svg">

        <title>SHOPLINK Intelligence</title>

        @livewireStyles
        <tallstackui:style />
        @vite(['resources/css/app.css', 'resources/js/app.js'])
        <script>
            try {
                if (localStorage.getItem('shoplink.sidebar.collapsed') === '1') {
                    document.documentElement.classList.add('sl-sidebar-collapsed');
                }
            } catch (error) {}
        </script>
        <style>
            body.dashboard-shell{margin:0;min-height:100vh;background:radial-gradient(circle at top left, rgba(100,116,139,.10), transparent 24%),radial-gradient(circle at top right, rgba(148,163,184,.12), transparent 26%),linear-gradient(180deg,#f8fafc 0%,#eef2f7 100%);color:#0f172a;font-family:ui-sans-serif,system-ui,sans-serif;-webkit-font-smoothing:antialiased}
            .dashboard-main{min-height:100vh}.dashboard-canvas,.sl-shell{max-width:1720px;margin-inline:auto}.sl-shell{padding-inline:1.25rem}.sl-shell-wide{max-width:1820px;padding-inline:1rem}.sl-card{border-radius:.75rem;background:#fff;border:1px solid #e2e8f0;box-shadow:0 10px 24px rgba(15,23,42,.05)}.sl-card-soft{border-radius:.75rem;background:#f8fafc;border:1px solid #e2e8f0;box-shadow:0 10px 24px rgba(15,23,42,.04)}
            .sl-btn-secondary,.sl-btn-primary,.sl-btn-soft{display:inline-flex;align-items:center;justify-content:center;gap:.5rem;border-radius:.5rem;padding:.625rem 1rem;font-size:.875rem;font-weight:600;text-decoration:none}.sl-btn-secondary,.sl-btn-soft{color:#334155;background:#f8fafc;border:1px solid #cbd5e1}.sl-btn-primary{background:#e11d48;color:#fff}.sl-pill{display:inline-flex;align-items:center;gap:.5rem;border-radius:.5rem;background:#f8fafc;border:1px solid #e2e8f0;padding:.375rem .75rem;font-size:.8125rem;color:#475569}
            .sl-form-label{display:block;margin-bottom:.375rem;font-size:.875rem;line-height:1.25rem;font-weight:700;color:#0f172a}
            .sl-table-head{display:none}.sl-table-row{display:grid;gap:.375rem;padding:.375rem 1rem}.sl-task-note{margin-top:.125rem;font-size:12px;line-height:1rem;font-weight:500;color:#64748b}.sl-status{display:inline-flex;align-items:center;border-radius:.375rem;padding:.25rem .5rem;font-size:12px;font-weight:700}.sl-avatar{display:inline-flex;height:2.25rem;width:2.25rem;align-items:center;justify-content:center;border-radius:9999px;background:#f4f4f5;color:#020617;font-size:.75rem;font-weight:700}.sl-task-center-row{padding-top:.2rem;padding-bottom:.2rem}.sl-task-center-row .sl-avatar{height:2.125rem;width:2.125rem;font-size:12px}
            @media (min-width:1024px){.dashboard-main{padding-left:16.75rem}.sl-table-head{display:grid;grid-template-columns:minmax(0,1fr) 64px 124px 92px;gap:.75rem;padding:.5rem 1rem;font-size:.75rem;font-weight:700;text-transform:uppercase;color:#64748b}.sl-table-row{grid-template-columns:minmax(0,1fr) 64px 124px 92px;align-items:center;gap:.75rem;padding:.375rem 1rem}}
            @media (min-width:1024px){.sl-sidebar{transition:width .18s ease}.dashboard-main{transition:padding-left .18s ease}.sl-sidebar-label,.sl-sidebar-kicker,.sl-sidebar-user-body,.sl-sidebar-logout{transition:opacity .12s ease}html.sl-sidebar-collapsed .sl-sidebar{width:4.75rem}html.sl-sidebar-collapsed .dashboard-main{padding-left:6rem}html.sl-sidebar-collapsed .sl-sidebar-label,html.sl-sidebar-collapsed .sl-sidebar-kicker,html.sl-sidebar-collapsed .sl-sidebar-user-body,html.sl-sidebar-collapsed .sl-sidebar-logout{display:none}html.sl-sidebar-collapsed .sl-sidebar-link{justify-content:center;padding-left:.625rem;padding-right:.625rem}html.sl-sidebar-collapsed .sl-sidebar-brand{justify-content:center;padding-left:.5rem;padding-right:.5rem}html.sl-sidebar-collapsed .sl-sidebar-section{padding:.5rem}html.sl-sidebar-collapsed .sl-sidebar-user{padding:.5rem;justify-content:center}html.sl-sidebar-collapsed .sl-sidebar-toggle svg{transform:rotate(180deg)}}
        </style>
    </head>
    <body class="dashboard-shell antialiased">
        <div class="min-h-screen dashboard-frame">
            @include('layouts.navigation')
            <div class="dashboard-main">
                <!-- Page Heading -->
                @isset($header)
                    <header class="dashboard-header-wrap">
                        <div class="{{ isset($headerShellClass) ? trim((string) $headerShellClass) : 'sl-shell pt-3' }}">
                            {{ $header }}
                        </div>
                    </header>
                @endisset

                <!-- Page Content -->
                <main class="pb-10">
                    {{ $slot }}
                </main>
            </div>
        </div>
        @livewireScripts
    </body>
</html>
