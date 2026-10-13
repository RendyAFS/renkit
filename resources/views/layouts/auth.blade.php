<!DOCTYPE html>
<html lang="{{ app()->getLocale() }}">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta http-equiv="X-UA-Compatible" content="ie=edge">
    <title>{{ $title ?? config('app.name', 'RenKit') }}</title>

    {{-- Init Theme --}}
    <script src="{{ Vite::asset('resources/js/utils/init-theme.js') }}"></script>

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
