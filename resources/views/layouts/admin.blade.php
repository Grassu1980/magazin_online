<!DOCTYPE html>
<html lang="ro">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name="description" content="Panou de Administrare - Magazin Online">
    <title>@yield('title', 'Admin Panel')</title>

    @if(setting('favicon'))
    <link rel="icon" type="image/x-icon" href="{{ Storage::url(setting('favicon')) }}">
    @endif

    <link rel="stylesheet" href="{{ asset('css/app.css') }}">
    <script src="https://unpkg.com/heroicons@1.0.6/dist/heroicons.min.js"></script>
    @stack('styles')
</head>
<body class="bg-slate-50 min-h-screen">
    <div id="adminSidebarBackdrop" class="fixed inset-0 bg-slate-900/50 hidden z-40 lg:hidden"></div>

    <aside id="adminSidebar" class="fixed inset-y-0 left-0 w-64 bg-slate-900 text-white -translate-x-full lg:translate-x-0 transition-transform z-50">
        <div class="h-16 px-6 flex items-center border-b border-white/10">
            <a href="{{ route('admin.dashboard') }}" class="font-semibold tracking-tight">
                Admin Panel
            </a>
        </div>

        <nav class="px-3 py-4 space-y-1">
            <a href="{{ route('admin.dashboard') }}" class="flex items-center gap-3 px-3 py-2 rounded-lg text-sm {{ request()->routeIs('admin.dashboard') ? 'bg-white/10 text-white' : 'text-white/80 hover:bg-white/10 hover:text-white' }}">
                <x-heroicon-outline-home class="w-5 h-5" />
                Dashboard
            </a>

            <a href="{{ route('admin.products.index') }}" class="flex items-center gap-3 px-3 py-2 rounded-lg text-sm {{ request()->routeIs('admin.products.*') ? 'bg-white/10 text-white' : 'text-white/80 hover:bg-white/10 hover:text-white' }}">
                <x-heroicon-outline-shopping-bag class="w-5 h-5" />
                Produse
            </a>

            <a href="{{ route('admin.categories.index') }}" class="flex items-center gap-3 px-3 py-2 rounded-lg text-sm {{ request()->routeIs('admin.categories.*') ? 'bg-white/10 text-white' : 'text-white/80 hover:bg-white/10 hover:text-white' }}">
                <x-heroicon-outline-tag class="w-5 h-5" />
                Categorii
            </a>

            <a href="{{ route('admin.orders.index') }}" class="flex items-center gap-3 px-3 py-2 rounded-lg text-sm {{ request()->routeIs('admin.orders.*') ? 'bg-white/10 text-white' : 'text-white/80 hover:bg-white/10 hover:text-white' }}">
                <x-heroicon-outline-shopping-cart class="w-5 h-5" />
                Comenzi
            </a>

            <a href="{{ route('admin.receipts.index') }}" class="flex items-center gap-3 px-3 py-2 rounded-lg text-sm {{ request()->routeIs('admin.receipts.*') ? 'bg-white/10 text-white' : 'text-white/80 hover:bg-white/10 hover:text-white' }}">
                <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor" class="w-5 h-5">
                    <path stroke-linecap="round" stroke-linejoin="round" d="M19.5 14.25v-2.625a3.375 3.375 0 00-3.375-3.375h-1.5A1.125 1.125 0 0113.5 7.125v-1.5a3.375 3.375 0 00-3.375-3.375H8.25m2.25 0H5.625c-.621 0-1.125.504-1.125 1.125v17.25c0 .621.504 1.125 1.125 1.125h12.75c.621 0 1.125-.504 1.125-1.125V11.25a9 9 0 00-9-9z" />
                </svg>
                Recepții (NIR)
            </a>

            <a href="{{ route('admin.suppliers.index') }}" class="flex items-center gap-3 px-3 py-2 rounded-lg text-sm {{ request()->routeIs('admin.suppliers.*') ? 'bg-white/10 text-white' : 'text-white/80 hover:bg-white/10 hover:text-white' }}">
                <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor" class="w-5 h-5">
                    <path stroke-linecap="round" stroke-linejoin="round" d="M2.25 21h19.5m-18-18v18m10.5-18v18m6-13.5V21M6.75 6.75h.75m-.75 3h.75m-.75 3h.75m3-6h.75m-.75 3h.75m-.75 3h.75M6.75 21v-3.375c0-.621.504-1.125 1.125-1.125h2.25c.621 0 1.125.504 1.125 1.125V21M3 3h12m-.75 4.5H21m-3.75 3.75h.008v.008h-.008v-.008zm0 3h.008v.008h-.008v-.008zm0 3h.008v.008h-.008v-.008zm0 3h.008v.008h-.008v-.008z" />
                </svg>
                Furnizori
            </a>

            <a href="{{ route('admin.users.index') }}" class="flex items-center gap-3 px-3 py-2 rounded-lg text-sm {{ request()->routeIs('admin.users.*') ? 'bg-white/10 text-white' : 'text-white/80 hover:bg-white/10 hover:text-white' }}">
                <x-heroicon-outline-users class="w-5 h-5" />
                Utilizatori
            </a>

            <div class="pt-4">
                <div class="px-3 text-xs font-semibold text-white/50 uppercase tracking-wider">Homepage</div>
                <div class="mt-2 space-y-1">
                    <a href="{{ route('admin.banners.index') }}" class="flex items-center gap-3 px-3 py-2 rounded-lg text-sm {{ request()->routeIs('admin.banners.*') ? 'bg-white/10 text-white' : 'text-white/80 hover:bg-white/10 hover:text-white' }}">
                        <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor" class="w-5 h-5">
                            <path stroke-linecap="round" stroke-linejoin="round" d="M2.25 15.75l5.159-5.159a2.25 2.25 0 013.182 0l5.159 5.159m-1.5-1.5l1.409-1.409a2.25 2.25 0 013.182 0l2.909 2.909m-18 3.75h16.5a1.5 1.5 0 001.5-1.5V6a1.5 1.5 0 00-1.5-1.5H3.75A1.5 1.5 0 002.25 6v12a1.5 1.5 0 001.5 1.5zm10.5-11.25h.008v.008h-.008V8.25zm.375 0a.375.375 0 11-.75 0 .375.375 0 01.75 0z" />
                        </svg>
                        Bannere
                    </a>
                    <a href="{{ route('admin.homepage-sections.index') }}" class="flex items-center gap-3 px-3 py-2 rounded-lg text-sm {{ request()->routeIs('admin.homepage-sections.*') ? 'bg-white/10 text-white' : 'text-white/80 hover:bg-white/10 hover:text-white' }}">
                        <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor" class="w-5 h-5">
                            <path stroke-linecap="round" stroke-linejoin="round" d="M3.75 6A2.25 2.25 0 016 3.75h2.25A2.25 2.25 0 0110.5 6v2.25a2.25 2.25 0 01-2.25 2.25H6a2.25 2.25 0 01-2.25-2.25V6zM3.75 15.75A2.25 2.25 0 016 13.5h2.25a2.25 2.25 0 012.25 2.25V18a2.25 2.25 0 01-2.25 2.25H6A2.25 2.25 0 013.75 18v-2.25zM13.5 6a2.25 2.25 0 012.25-2.25H18A2.25 2.25 0 0120.25 6v2.25A2.25 2.25 0 0118 10.5h-2.25a2.25 2.25 0 01-2.25-2.25V6zM13.5 15.75a2.25 2.25 0 012.25-2.25H18a2.25 2.25 0 012.25 2.25V18A2.25 2.25 0 0118 20.25h-2.25A2.25 2.25 0 0113.5 18v-2.25z" />
                        </svg>
                        Secțiuni Homepage
                    </a>
                </div>
            </div>

            <div class="pt-4">
                <div class="px-3 text-xs font-semibold text-white/50 uppercase tracking-wider">Setări</div>
                <div class="mt-2 space-y-1">
                    <a href="{{ route('admin.settings.general.edit') }}" class="flex items-center gap-3 px-3 py-2 rounded-lg text-sm {{ request()->routeIs('admin.settings.general.*') ? 'bg-white/10 text-white' : 'text-white/80 hover:bg-white/10 hover:text-white' }}">
                        <x-heroicon-outline-cog class="w-5 h-5" />
                        Generale
                    </a>
                    <a href="{{ route('admin.settings.contact.edit') }}" class="flex items-center gap-3 px-3 py-2 rounded-lg text-sm {{ request()->routeIs('admin.settings.contact.*') ? 'bg-white/10 text-white' : 'text-white/80 hover:bg-white/10 hover:text-white' }}">
                        <x-heroicon-outline-envelope class="w-5 h-5" />
                        Contact
                    </a>
                </div>
            </div>

            <div class="pt-4 border-t border-white/10">
                <a href="{{ route('home') }}" class="flex items-center gap-3 px-3 py-2 rounded-lg text-sm text-white/80 hover:bg-white/10 hover:text-white">
                    <x-heroicon-outline-external-link class="w-5 h-5" />
                    Vezi site-ul
                </a>

                <form method="POST" action="{{ route('logout') }}" class="mt-1">
                    @csrf
                    <button type="submit" class="flex items-center gap-3 px-3 py-2 rounded-lg text-sm text-white/80 hover:bg-white/10 hover:text-white w-full text-left">
                        <x-heroicon-outline-arrow-right-on-rectangle class="w-5 h-5" />
                        Deconectare
                    </button>
                </form>
            </div>
        </nav>
    </aside>

    <div class="lg:pl-64 flex flex-col min-h-screen">
        <header class="sticky top-0 z-30 bg-white border-b border-slate-200">
            <div class="h-16 px-4 sm:px-6 lg:px-8 flex items-center justify-between gap-4">
                <div class="flex items-center gap-3 min-w-0">
                    <button type="button" id="adminSidebarOpen" class="lg:hidden inline-flex items-center justify-center w-10 h-10 rounded-lg border border-slate-200 text-slate-700 hover:bg-slate-50">
                        <x-heroicon-outline-menu class="w-6 h-6" />
                    </button>

                    <div class="min-w-0">
                        <div class="text-xs text-slate-500 truncate">
                            @yield('breadcrumbs', 'Admin')
                        </div>
                        <div class="text-lg font-semibold text-slate-900 truncate">
                            @yield('page_title', 'Dashboard')
                        </div>
                    </div>
                </div>

                <div class="flex items-center gap-3">
                    <div class="hidden sm:block text-sm text-slate-700">{{ Auth::user()->name }}</div>
                </div>
            </div>
        </header>

        <main class="px-4 sm:px-6 lg:px-8 py-6 flex-1">
            @if(session('success'))
                <div class="mb-4 rounded-lg border border-emerald-200 bg-emerald-50 px-4 py-3 text-emerald-800">
                    {{ session('success') }}
                </div>
            @endif

            @if(session('error'))
                <div class="mb-4 rounded-lg border border-rose-200 bg-rose-50 px-4 py-3 text-rose-800">
                    {{ session('error') }}
                </div>
            @endif

            @yield('content')
        </main>
    </div>

    <script src="{{ asset('js/app.js') }}"></script>
    <script>
        const sidebar = document.getElementById('adminSidebar');
        const backdrop = document.getElementById('adminSidebarBackdrop');
        const openBtn = document.getElementById('adminSidebarOpen');

        function openSidebar() {
            sidebar.classList.remove('-translate-x-full');
            backdrop.classList.remove('hidden');
        }

        function closeSidebar() {
            sidebar.classList.add('-translate-x-full');
            backdrop.classList.add('hidden');
        }

        openBtn?.addEventListener('click', openSidebar);
        backdrop?.addEventListener('click', closeSidebar);
    </script>

    @stack('scripts')
</body>
</html>
