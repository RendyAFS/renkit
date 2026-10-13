@extends('layouts.main', ['title' => 'Dashboard'])

@section('content')
    <div class="space-y-6">
        <div>
            <h1 class="text-3xl font-bold tracking-tight">Dashboard</h1>
            <p class="text-sm text-(--color-dark-gray) mt-1">Welcome to your {{ config('app.name', 'RenKit') }} application!</p>
        </div>

        <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-4 gap-4">
            <div class="p-6 rounded-2xl bg-(--color-light) dark:bg-(--color-dark) border border-(--color-gray)/20 shadow-sm flex items-center gap-4">
                <div class="size-12 rounded-xl bg-(--color-primary)/10 text-(--color-primary) flex items-center justify-center">
                    <i data-lucide="layers" class="size-6"></i>
                </div>
                <div>
                    <p class="text-xs font-semibold text-(--color-dark-gray) uppercase tracking-wider">Status</p>
                    <h3 class="text-xl font-bold">Ready</h3>
                </div>
            </div>

            <div class="p-6 rounded-2xl bg-(--color-light) dark:bg-(--color-dark) border border-(--color-gray)/20 shadow-sm flex items-center gap-4">
                <div class="size-12 rounded-xl bg-(--color-success)/10 text-(--color-success) flex items-center justify-center">
                    <i data-lucide="shield-check" class="size-6"></i>
                </div>
                <div>
                    <p class="text-xs font-semibold text-(--color-dark-gray) uppercase tracking-wider">UI Engine</p>
                    <h3 class="text-xl font-bold">Preline v3</h3>
                </div>
            </div>

            <div class="p-6 rounded-2xl bg-(--color-light) dark:bg-(--color-dark) border border-(--color-gray)/20 shadow-sm flex items-center gap-4">
                <div class="size-12 rounded-xl bg-(--color-info)/10 text-(--color-info) flex items-center justify-center">
                    <i data-lucide="palette" class="size-6"></i>
                </div>
                <div>
                    <p class="text-xs font-semibold text-(--color-dark-gray) uppercase tracking-wider">Styling</p>
                    <h3 class="text-xl font-bold">Tailwind CSS v4</h3>
                </div>
            </div>

            <div class="p-6 rounded-2xl bg-(--color-light) dark:bg-(--color-dark) border border-(--color-gray)/20 shadow-sm flex items-center gap-4">
                <div class="size-12 rounded-xl bg-(--color-warning)/10 text-(--color-warning) flex items-center justify-center">
                    <i data-lucide="cpu" class="size-6"></i>
                </div>
                <div>
                    <p class="text-xs font-semibold text-(--color-dark-gray) uppercase tracking-wider">RenKit</p>
                    <h3 class="text-xl font-bold">v{{ config('renkit.version', '1.1.0') }}</h3>
                </div>
            </div>
        </div>

        <div class="p-6 rounded-2xl bg-(--color-light) dark:bg-(--color-dark) border border-(--color-gray)/20 shadow-sm">
            <h2 class="text-lg font-bold mb-2">Getting Started</h2>
            <p class="text-sm text-(--color-dark-gray) mb-4">
                Generate your next module effortlessly using the command line:
            </p>
            <div class="p-4 rounded-xl bg-(--color-light-gray) dark:bg-(--color-dark-slate) font-mono text-sm text-(--color-primary) flex items-center justify-between">
                <span>php artisan renkit:module {ModelName} [--simple|--resource]</span>
            </div>
        </div>
    </div>
@endsection
