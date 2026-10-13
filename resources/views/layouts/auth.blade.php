<!DOCTYPE html>
<html lang="{{ app()->getLocale() }}">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta http-equiv="X-UA-Compatible" content="ie=edge">
    <title>{{ $title ?? config('app.name', 'RenKit') }}</title>

    {{-- Init Theme --}}
    <script>
        (function() {
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

    {{-- Styles Vite --}}
    @vite(['resources/js/app.js', 'resources/css/app.css'])

    @stack('styles')
</head>

<body>
    <div class="bg-(--color-light) text-(--color-dark) dark:bg-(--color-dark) dark:text-(--color-light)">
        @yield('content')
    </div>

    @if (Route::has('login'))
        @routes
    @endif

    <x-custom-alert />

    @stack('scripts')
</body>

</html>
