<?php

return [

    /*
    |--------------------------------------------------------------------------
    | RenKit Information
    |--------------------------------------------------------------------------
    */
    'version' => '1.1.4',
    'developer' => 'RendyAFS',
    'github' => 'https://github.com/RendyAFS/renkit',

    /*
    |--------------------------------------------------------------------------
    | UI & CSS Configuration
    |--------------------------------------------------------------------------
    |
    | RenKit is built on Tailwind CSS by default. The interactive components
    | (modals, dropdowns, alerts) use reusable Blade component wrappers
    | (<x-modal>, <x-datatable>) which default to Preline UI, but can be
    | easily adapted or overridden by the user.
    |
    */
    'ui' => [
        'framework' => env('RENKIT_UI_FRAMEWORK', 'preline'),
        'use_component_wrappers' => true,
        'datatable_client_side' => true,
    ],

    /*
    |--------------------------------------------------------------------------
    | Optional Features
    |--------------------------------------------------------------------------
    |
    | Configure optional integrations. If a feature is disabled, RenKit will
    | gracefully omit related code without breaking the generated CRUD.
    |
    */
    'features' => [
        // Frontend toolchain with Tailwind CSS v4, Preline UI, and Vite globbing
        'frontend_toolchain' => env('RENKIT_FRONTEND_TOOLCHAIN', true),

        // Authentication scaffolding with Fortify & Preline UI Views
        'auth_scaffold' => env('RENKIT_AUTH_SCAFFOLD', true),

        // Multi-language translation support via lang/{locale}/
        'localization' => env('RENKIT_LOCALIZATION', true),

        // Light, dark, and auto theme support via Tailwind & CSS variables
        'theme' => env('RENKIT_THEME', true),

        // Spatie Laravel Permission authorization in Controller and Views
        'permission' => env('RENKIT_PERMISSION', true),

        // Automatic registration in config/menu.php
        'menu' => env('RENKIT_MENU_INTEGRATION', true),
    ],

    /*
    |--------------------------------------------------------------------------
    | Localization (Optional Multi-Language)
    |--------------------------------------------------------------------------
    |
    | When localization is enabled, RenKit UI components (<x-modal>, <x-datatable>,
    | <x-cardgrid>, <x-button-loading>, <x-custom-alert>) dynamically load
    | translations from lang/{locale}/ with safe Indonesian/English fallbacks.
    |
    */
    'localization' => [
        'locales' => ['id', 'en'],
        'default_locale' => env('RENKIT_LOCALE', 'id'),
        'auto_translate_models' => true,
    ],

    /*
    |--------------------------------------------------------------------------
    | Paths & Directories
    |--------------------------------------------------------------------------
    */
    'paths' => [
        'routes_dir' => 'routes/web',
        'routes_main' => 'routes/web.php',
        'menu_file' => 'config/menu.php',
        'views_dir' => 'resources/views',
        'js_dir' => 'resources/js/pages',
        'stubs_dir' => null, // Set path to override default stubs (or via vendor:publish)
    ],

    /*
    |--------------------------------------------------------------------------
    | Generator Defaults
    |--------------------------------------------------------------------------
    */
    'generator' => [
        'default_mode' => 'simple', // 'simple' (modal) or 'resource' (full page)
        'generate_migration' => true,
        'generate_model' => true,
        'use_soft_deletes' => true,
        'use_userstamps' => true,
    ],

];
