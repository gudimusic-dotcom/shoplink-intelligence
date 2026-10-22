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

        <!-- Fonts -->
        <link rel="preconnect" href="https://fonts.bunny.net">
        <link href="https://fonts.bunny.net/css?family=figtree:400,500,600,700,800&display=swap" rel="stylesheet" />
        <link href="https://fonts.bunny.net/css?family=manrope:400,500,600,700,800&display=swap" rel="stylesheet" />

        <tallstackui:style />
        @vite(['resources/css/app.css', 'resources/js/app.js'])
        <style>
            body.dashboard-shell{margin:0;min-height:100vh;background:#111b31;color:#f8fafc;font-family:Figtree,ui-sans-serif,system-ui,sans-serif;-webkit-font-smoothing:antialiased}
            .sl-card{border-radius:.75rem;background:rgba(30,41,59,.78)}
            .sl-form-label{display:block;margin-bottom:.375rem;font-size:.875rem;line-height:1.25rem;font-weight:700;color:#e2e8f0}
            .ts-field-reset{background:rgba(15,23,42,.92)!important;color:#f8fafc!important;border:1px solid rgba(100,116,139,.46)!important;caret-color:#f8fafc!important}
            .ts-field-reset::placeholder{color:rgba(148,163,184,.88)!important}
            button[type=submit]{display:inline-flex;align-items:center;justify-content:center;border-radius:.5rem;background:#e11d48;padding:.625rem 1rem;font-size:.875rem;font-weight:700;color:white;border:0}
        </style>
    </head>
    <body class="dashboard-shell antialiased">
        <div class="flex min-h-screen flex-col items-center justify-center px-4 py-8">
            <div class="mb-6">
                <a href="/">
                    <x-application-logo class="h-14 w-14 fill-current text-slate-200" />
                </a>
            </div>

            <div class="sl-card w-full max-w-md px-6 py-6">
                {{ $slot }}
            </div>
        </div>
    </body>
</html>
