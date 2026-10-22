<nav class="sticky top-0 z-50 border-b border-slate-200 bg-white/95 px-4 py-3 backdrop-blur lg:hidden">
    <div class="flex items-center justify-between gap-3">
        <a href="{{ route('dashboard') }}" class="flex min-w-0 items-center gap-2 text-slate-900">
            <span class="inline-flex h-8 w-8 items-center justify-center rounded-full bg-rose-600 text-xs font-semibold text-white">S</span>
            <span class="truncate text-sm font-semibold">SHOPLINK</span>
        </a>

        <div class="flex items-center gap-2">
            <form method="POST" action="{{ route('logout') }}">
                @csrf
                <button type="submit" class="sl-btn-secondary !px-3 !py-2">Odhlásiť</button>
            </form>
        </div>
    </div>

    <div class="mt-3 flex items-center gap-2 overflow-x-auto">
        <div class="flex items-center gap-2">
            <a href="{{ route('dashboard') }}" class="sl-btn-secondary !px-3 !py-2 {{ request()->routeIs('dashboard') ? '!bg-rose-600 !text-white' : '' }}">Dashboard</a>
            <a href="{{ route('tasks.index') }}" class="sl-btn-secondary !px-3 !py-2 {{ request()->routeIs('tasks.*') ? '!bg-rose-600 !text-white' : '' }}">Tasky</a>
            @if (Auth::user()?->canAccessSalesIntelligence())
                <a href="{{ route('sales.index') }}" class="sl-btn-secondary !px-3 !py-2 {{ request()->routeIs('sales.*') ? '!bg-rose-600 !text-white' : '' }}">Sales</a>
            @endif
            <a href="{{ route('monitors.index') }}" class="sl-btn-secondary !px-3 !py-2 {{ request()->routeIs('monitors.*') ? '!bg-rose-600 !text-white' : '' }}">Monitors</a>
            @if (Auth::user()?->canManageUsers())
                <a href="{{ route('product-stats.index') }}" class="sl-btn-secondary !px-3 !py-2 {{ request()->routeIs('product-stats.*') ? '!bg-rose-600 !text-white' : '' }}">Product Stats</a>
            @endif
            @if (Auth::user()?->canManageUsers())
                <a href="{{ route('users.index') }}" class="sl-btn-secondary !px-3 !py-2 {{ request()->routeIs('users.*') ? '!bg-rose-600 !text-white' : '' }}">Používatelia</a>
            @endif
            <a href="{{ route('settings.index') }}" class="sl-btn-secondary !px-3 !py-2 {{ request()->routeIs('settings.*') ? '!bg-rose-600 !text-white' : '' }}">Nastavenia</a>
        </div>
    </div>
</nav>

<nav class="sl-sidebar fixed left-4 bottom-4 top-8 z-40 hidden w-64 lg:block">
    <aside class="sl-card flex h-full flex-col p-2.5">
        <div class="space-y-3">
            <div class="flex items-center gap-2">
                <a href="{{ route('dashboard') }}" class="sl-sidebar-brand flex min-w-0 flex-1 items-center gap-3 rounded-xl bg-slate-50 px-3 py-3 text-slate-900">
                <span class="inline-flex h-10 w-10 items-center justify-center rounded-full bg-rose-600 text-sm font-semibold text-white">S</span>
                <span class="sl-sidebar-label min-w-0">
                    <strong class="block truncate text-sm font-semibold tracking-tight">SHOPLINK Intelligence</strong>
                    <small class="block text-xs text-slate-500">Daily workspace</small>
                </span>
                </a>
                <button type="button" class="sl-sidebar-toggle sl-btn-secondary !h-10 !w-10 !px-0" aria-label="Zbaliť menu" onclick="window.ShoplinkSidebar?.toggle()">
                    <svg viewBox="0 0 24 24" class="h-4 w-4 transition" fill="none" stroke="currentColor" stroke-width="2">
                        <path d="M15 6l-6 6 6 6"></path>
                    </svg>
                </button>
            </div>

            <section class="sl-sidebar-section sl-card-soft p-2.5">
                <p class="sl-sidebar-kicker sl-kicker px-2">Workspace</p>
                <div class="mt-2.5 space-y-1">
                    <a href="{{ route('dashboard') }}"
                        class="sl-sidebar-link flex items-center gap-3 rounded-lg px-3 py-2.5 text-sm font-medium transition {{ request()->routeIs('dashboard') ? 'bg-rose-600 text-white' : 'text-slate-700 hover:bg-slate-100' }}">
                        <span class="inline-flex h-8 w-8 items-center justify-center rounded-lg {{ request()->routeIs('dashboard') ? 'bg-white/15 text-white' : 'bg-slate-100 text-slate-700' }}">
                            <svg viewBox="0 0 24 24" class="h-4 w-4" fill="none" stroke="currentColor" stroke-width="1.8">
                                <rect x="3" y="3" width="7" height="7" rx="2"></rect>
                                <rect x="14" y="3" width="7" height="7" rx="2"></rect>
                                <rect x="3" y="14" width="7" height="7" rx="2"></rect>
                                <rect x="14" y="14" width="7" height="7" rx="2"></rect>
                            </svg>
                        </span>
                        <span class="sl-sidebar-label">Dashboard</span>
                    </a>

                    <a href="{{ route('tasks.index') }}"
                        class="sl-sidebar-link flex items-center gap-3 rounded-lg px-3 py-2.5 text-sm font-medium transition {{ request()->routeIs('tasks.*') ? 'bg-rose-600 text-white' : 'text-slate-700 hover:bg-slate-100' }}">
                        <span class="inline-flex h-8 w-8 items-center justify-center rounded-lg {{ request()->routeIs('tasks.*') ? 'bg-white/15 text-white' : 'bg-slate-100 text-slate-700' }}">
                            <svg viewBox="0 0 24 24" class="h-4 w-4" fill="none" stroke="currentColor" stroke-width="1.8">
                                <path d="M4 6h16"></path>
                                <path d="M4 12h16"></path>
                                <path d="M4 18h10"></path>
                            </svg>
                        </span>
                        <span class="sl-sidebar-label">Task Center</span>
                    </a>

                    @if (Auth::user()?->canAccessSalesIntelligence())
                        <a href="{{ route('sales.index') }}"
                            class="sl-sidebar-link flex items-center gap-3 rounded-lg px-3 py-2.5 text-sm font-medium transition {{ request()->routeIs('sales.*') ? 'bg-rose-600 text-white' : 'text-slate-700 hover:bg-slate-100' }}">
                            <span class="inline-flex h-8 w-8 items-center justify-center rounded-lg {{ request()->routeIs('sales.*') ? 'bg-white/15 text-white' : 'bg-slate-100 text-slate-700' }}">
                                <svg viewBox="0 0 24 24" class="h-4 w-4" fill="none" stroke="currentColor" stroke-width="1.8">
                                    <path d="M3 3v18h18"></path>
                                    <path d="M7 15l4-4 3 3 5-7"></path>
                                </svg>
                            </span>
                            <span class="sl-sidebar-label">Sales Intelligence</span>
                        </a>
                    @endif

                    <a href="{{ route('monitors.index') }}"
                        class="sl-sidebar-link flex items-center gap-3 rounded-lg px-3 py-2.5 text-sm font-medium transition {{ request()->routeIs('monitors.*') ? 'bg-rose-600 text-white' : 'text-slate-700 hover:bg-slate-100' }}">
                        <span class="inline-flex h-8 w-8 items-center justify-center rounded-lg {{ request()->routeIs('monitors.*') ? 'bg-white/15 text-white' : 'bg-slate-100 text-slate-700' }}">
                            <svg viewBox="0 0 24 24" class="h-4 w-4" fill="none" stroke="currentColor" stroke-width="1.8">
                                <path d="M5 12h4"></path>
                                <path d="M15 12h4"></path>
                                <path d="M12 5v4"></path>
                                <path d="M12 15v4"></path>
                                <circle cx="12" cy="12" r="2.5"></circle>
                            </svg>
                        </span>
                        <span class="sl-sidebar-label">Monitors</span>
                    </a>

                    @if (Auth::user()?->canManageUsers())
                        <a href="{{ route('product-stats.index') }}"
                            class="sl-sidebar-link flex items-center gap-3 rounded-lg px-3 py-2.5 text-sm font-medium transition {{ request()->routeIs('product-stats.*') ? 'bg-rose-600 text-white' : 'text-slate-700 hover:bg-slate-100' }}">
                            <span class="inline-flex h-8 w-8 items-center justify-center rounded-lg {{ request()->routeIs('product-stats.*') ? 'bg-white/15 text-white' : 'bg-slate-100 text-slate-700' }}">
                                <svg viewBox="0 0 24 24" class="h-4 w-4" fill="none" stroke="currentColor" stroke-width="1.8">
                                    <path d="M4 6h16"></path>
                                    <path d="M4 12h16"></path>
                                    <path d="M4 18h8"></path>
                                    <path d="M15 17l2 2 3-4"></path>
                                </svg>
                            </span>
                            <span class="sl-sidebar-label">Product Stats</span>
                        </a>
                    @endif

                    @if (Auth::user()?->canManageUsers())
                        <a href="{{ route('users.index') }}"
                            class="sl-sidebar-link flex items-center gap-3 rounded-lg px-3 py-2.5 text-sm font-medium transition {{ request()->routeIs('users.*') ? 'bg-rose-600 text-white' : 'text-slate-700 hover:bg-slate-100' }}">
                            <span class="inline-flex h-8 w-8 items-center justify-center rounded-lg {{ request()->routeIs('users.*') ? 'bg-white/15 text-white' : 'bg-slate-100 text-slate-700' }}">
                                <svg viewBox="0 0 24 24" class="h-4 w-4" fill="none" stroke="currentColor" stroke-width="1.8">
                                    <path d="M16 21v-2a4 4 0 0 0-4-4H6a4 4 0 0 0-4 4v2"></path>
                                    <circle cx="9" cy="7" r="4"></circle>
                                    <path d="M22 21v-2a4 4 0 0 0-3-3.87"></path>
                                    <path d="M16 3.13a4 4 0 0 1 0 7.75"></path>
                                </svg>
                            </span>
                            <span class="sl-sidebar-label">Používatelia</span>
                        </a>
                    @endif

                    <a href="{{ route('settings.index') }}"
                        class="sl-sidebar-link flex items-center gap-3 rounded-lg px-3 py-2.5 text-sm font-medium transition {{ request()->routeIs('settings.*') ? 'bg-rose-600 text-white' : 'text-slate-700 hover:bg-slate-100' }}">
                        <span class="inline-flex h-8 w-8 items-center justify-center rounded-lg {{ request()->routeIs('settings.*') ? 'bg-white/15 text-white' : 'bg-slate-100 text-slate-700' }}">
                            <svg viewBox="0 0 24 24" class="h-4 w-4" fill="none" stroke="currentColor" stroke-width="1.8">
                                <path d="M12 15.5A3.5 3.5 0 1 0 12 8a3.5 3.5 0 0 0 0 7.5Z"></path>
                                <path d="M19.4 15a1.7 1.7 0 0 0 .34 1.88l.06.06a2 2 0 1 1-2.83 2.83l-.06-.06A1.7 1.7 0 0 0 15 19.4a1.7 1.7 0 0 0-1 .6 1.7 1.7 0 0 0-.4 1.1V21a2 2 0 1 1-4 0v-.1A1.7 1.7 0 0 0 8 19.4a1.7 1.7 0 0 0-1.88.34l-.06.06a2 2 0 1 1-2.83-2.83l.06-.06A1.7 1.7 0 0 0 4.6 15a1.7 1.7 0 0 0-.6-1 1.7 1.7 0 0 0-1.1-.4H3a2 2 0 1 1 0-4h.1A1.7 1.7 0 0 0 4.6 8a1.7 1.7 0 0 0-.34-1.88l-.06-.06a2 2 0 1 1 2.83-2.83l.06.06A1.7 1.7 0 0 0 9 4.6a1.7 1.7 0 0 0 1-.6 1.7 1.7 0 0 0 .4-1.1V3a2 2 0 1 1 4 0v.1A1.7 1.7 0 0 0 16 4.6a1.7 1.7 0 0 0 1.88-.34l.06-.06a2 2 0 1 1 2.83 2.83l-.06.06A1.7 1.7 0 0 0 19.4 9c.2.3.5.6.9.6h.7a2 2 0 1 1 0 4h-.7a1.7 1.7 0 0 0-.9 1.4Z"></path>
                            </svg>
                        </span>
                        <span class="sl-sidebar-label">Nastavenia</span>
                    </a>

                </div>
            </section>

        </div>

        <div class="sl-sidebar-user mt-auto rounded-2xl border border-slate-200 bg-slate-50 p-3">
            <div class="flex items-center gap-3">
                <span class="sl-avatar">{{ strtoupper(substr(Auth::user()->name, 0, 1)) }}</span>
                <div class="sl-sidebar-user-body min-w-0">
                    <strong class="block truncate text-sm text-slate-900">{{ Auth::user()->name }}</strong>
                    <small class="block truncate text-xs text-slate-500">{{ Auth::user()->roles->pluck('name')->implode(', ') ?: 'Bez roly' }}</small>
                </div>
            </div>

            <form method="POST" action="{{ route('logout') }}" class="sl-sidebar-logout mt-3">
                @csrf
                <button type="submit" class="sl-btn-secondary w-full justify-center">Odhlásiť sa</button>
            </form>
        </div>
    </aside>
</nav>
<script>
    window.ShoplinkSidebar = window.ShoplinkSidebar || {
        toggle() {
            const collapsed = !document.documentElement.classList.contains('sl-sidebar-collapsed');
            document.documentElement.classList.toggle('sl-sidebar-collapsed', collapsed);
            try {
                localStorage.setItem('shoplink.sidebar.collapsed', collapsed ? '1' : '0');
            } catch (error) {}
        },
    };
</script>
