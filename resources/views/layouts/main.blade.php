<!DOCTYPE html>
<html lang="{{ app()->getLocale() }}">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta http-equiv="X-UA-Compatible" content="ie=edge">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <script>
        (function() {
            try {
                var mode = localStorage.getItem('sidebar:mode') || 'sidebar';
                var isDesktop = window.matchMedia('(min-width: 1024px)').matches;
                if (!isDesktop || mode === 'floating') {
                    document.documentElement.classList.add('sidebar-floating-init');
                }
            } catch (e) {}
        })();
    </script>
    <title>{{ $title ?? config('app.name', 'RenKit') }}</title>

    <script>
        (function() {
            try {
                var params = new URLSearchParams(window.location.search);
                var hasValues = [...params.entries()].some(function (entry) { return entry[1] !== ""; });
                if (!hasValues) {
                    var key = "filters:" + window.location.pathname;
                    var stored = sessionStorage.getItem(key);
                    if (stored) { window.location.replace(window.location.pathname + "?" + stored); }
                }
            } catch (e) {}

            try {
                var html = document.documentElement;
                var storedTheme = localStorage.getItem("hs_theme") || "auto";
                var prefersDark = window.matchMedia("(prefers-color-scheme: dark)").matches;
                var isDark = storedTheme === "dark" || (storedTheme === "auto" && prefersDark);
                html.classList.toggle("dark", isDark);
                html.classList.toggle("light", !isDark);
            } catch (e) {}
        })();
    </script>

    @vite(['resources/css/app.css'])

    @stack('styles')
</head>

<body>
    <x-sidebar />

    <div id="app-content-area" class="transition-all duration-300 lg:ms-64 hs-overlay-minified:lg:ms-14">
        <div class="min-h-screen flex flex-col">
            <x-navbar />

            <main
                class="flex-1 px-4 md:px-10 py-8
                bg-(--color-light-gray) dark:bg-(--color-dark-slate)
                text-(--color-dark) dark:text-(--color-light)">
                @yield('content')
            </main>
        </div>
    </div>

    @routes

    {{-- Offline Vendor Scripts --}}
    <script src="{{ asset('js/jquery-3.6.0.min.js') }}"></script>
    <script src="{{ asset('js/blockUi.js') }}"></script>

    @if(View::exists('layouts.lang'))
        @include('layouts.lang')
    @endif

    <x-custom-alert />

    @vite(['resources/js/app.js'])

    @stack('scripts')
</body>

</html>
