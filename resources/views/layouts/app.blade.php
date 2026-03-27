<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}" class="h-full">

<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta name="csrf-token" content="{{ csrf_token() }}">

    <!-- Favicon icon -->
    <link rel="apple-touch-icon" sizes="180x180" href="{{ asset('assets/images/favicon/apple-touch-icon.png') }}" />
    <link rel="icon" type="image/png" sizes="32x32" href="{{ asset('assets/images/favicon/favicon-32x32.png') }}" />
    <link rel="icon" type="image/png" sizes="16x16" href="{{ asset('assets/images/favicon/favicon-16x16.png') }}" />
    <link rel="manifest" href="{{ asset('assets/images/favicon/site.webmanifest') }}" />

    <title>{{ $title ?? 'Dashboard' }} | Learn=>Audit : ISO 2700x</title>

    <!-- Fonts — same as welcome page -->
    <link rel="preconnect" href="https://fonts.bunny.net">
    <link href="https://fonts.bunny.net/css?family=instrument-sans:400,500,600,700" rel="stylesheet" />

    <!-- Tabler Icons — same as welcome page -->
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/@tabler/icons-webfont@latest/dist/tabler-icons.min.css" />

    <!-- Vite assets — same as welcome page -->
    @vite(['resources/css/app.css', 'resources/js/app.js'])

    <!-- Alpine.js -->
    <script defer src="https://cdn.jsdelivr.net/npm/alpinejs@3.x.x/dist/cdn.min.js"></script>

    <!-- Alpine stores (sidebar + theme) -->
    <script>
        document.addEventListener('alpine:init', () => {

            /* ── Theme store ────────────────────────────────────────── */
            Alpine.store('theme', {
                init() {
                    const saved  = localStorage.getItem('theme');
                    const system = window.matchMedia('(prefers-color-scheme: dark)').matches ? 'dark' : 'light';
                    this.theme   = saved || system;
                    this.updateTheme();
                },
                theme: 'light',
                toggle() {
                    this.theme = this.theme === 'light' ? 'dark' : 'light';
                    localStorage.setItem('theme', this.theme);
                    this.updateTheme();
                },
                updateTheme() {
                    document.documentElement.classList.toggle('dark', this.theme === 'dark');
                }
            });

            /* ── Sidebar store ──────────────────────────────────────── */
            Alpine.store('sidebar', {
                isExpanded:   window.innerWidth >= 1280,
                isMobileOpen: false,
                isHovered:    false,

                toggleExpanded()     { this.isExpanded = !this.isExpanded; this.isMobileOpen = false; },
                toggleMobileOpen()   { this.isMobileOpen = !this.isMobileOpen; },
                setMobileOpen(val)   { this.isMobileOpen = val; },
                setHovered(val)      {
                    if (window.innerWidth >= 1280 && !this.isExpanded) this.isHovered = val;
                }
            });
        });
    </script>

    <!-- Prevent dark-mode flash -->
    <script>
        (function () {
            const t = localStorage.getItem('theme')
                   || (window.matchMedia('(prefers-color-scheme: dark)').matches ? 'dark' : 'light');
            if (t === 'dark') document.documentElement.classList.add('dark');
        })();
    </script>

    <!-- Styles moved to resources/css/app.css -->
    @stack('styles')
</head>

<body
    x-data="{ loaded: true }"
    x-init="
        $store.sidebar.isExpanded = window.innerWidth >= 1280;
        const checkMobile = () => {
            if (window.innerWidth < 1280) {
                $store.sidebar.setMobileOpen(false);
                $store.sidebar.isExpanded = false;
            } else {
                $store.sidebar.isMobileOpen = false;
                $store.sidebar.isExpanded = true;
            }
        };
        window.addEventListener('resize', checkMobile);
    "
    class="">

    <div class="min-h-screen xl:flex">
        @include('layouts.backdrop')
        @include('layouts.sidebar')

        <!-- Main content wrapper — shifts with sidebar -->
        <div class="flex-1 transition-all duration-300 ease-in-out"
            :class="{
                'xl:ml-[290px]': $store.sidebar.isExpanded || $store.sidebar.isHovered,
                'xl:ml-[90px]':  !$store.sidebar.isExpanded && !$store.sidebar.isHovered,
                'ml-0':           $store.sidebar.isMobileOpen
            }">

            <!-- App header — carries welcome-page nav style -->
            @include('layouts.app-header')

            <!-- Page content -->
            <div class="p-4 mx-auto max-w-screen-2xl md:p-6">
                @yield('content')
            </div>
        </div>
    </div>

    @stack('scripts')
</body>

</html>