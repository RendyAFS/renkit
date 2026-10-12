<?php

namespace RendyAFS\RenKit\Support;

use Illuminate\Support\Facades\File;
use Spatie\Permission\Models\Permission;
use Tighten\Ziggy\Ziggy;

class EnvironmentDetector
{
    /**
     * Inspect all system requirements and dependencies.
     *
     * @return array<string, array{status: bool, label: string, message: string}>
     */
    public function inspect(): array
    {
        return [
            'php_version' => [
                'status' => version_compare(PHP_VERSION, '8.2.0', '>='),
                'label' => 'PHP Version',
                'message' => 'PHP '.PHP_VERSION.' (Requirement: >= 8.2)',
            ],
            'laravel_version' => [
                'status' => true,
                'label' => 'Laravel Framework',
                'message' => 'Laravel '.app()->version(),
            ],
            'ziggy' => [
                'status' => class_exists(Ziggy::class) || class_exists(\Tightenco\Ziggy\Ziggy::class),
                'label' => 'Ziggy (Route Provider)',
                'message' => (class_exists(Ziggy::class) || class_exists(\Tightenco\Ziggy\Ziggy::class))
                    ? 'Installed and available'
                    : 'Not found (recommended for JS route helper)',
            ],
            'spatie_permission' => [
                'status' => class_exists(Permission::class),
                'label' => 'Spatie Permission',
                'message' => class_exists(Permission::class)
                    ? 'Installed and active'
                    : 'Optional: Not installed (graceful fallback)',
            ],
            'tailwind_css' => [
                'status' => File::exists(base_path('package.json')) && str_contains(File::get(base_path('package.json')), 'tailwindcss'),
                'label' => 'Tailwind CSS',
                'message' => (File::exists(base_path('package.json')) && str_contains(File::get(base_path('package.json')), 'tailwindcss'))
                    ? 'Detected in package.json'
                    : 'Not detected',
            ],
            'preline_ui' => [
                'status' => File::exists(base_path('package.json')) && str_contains(File::get(base_path('package.json')), 'preline'),
                'label' => 'Preline UI',
                'message' => (File::exists(base_path('package.json')) && str_contains(File::get(base_path('package.json')), 'preline'))
                    ? 'Detected in package.json (Default UI driver)'
                    : 'Optional: Adaptable via Blade wrappers',
            ],
            'menu_config' => [
                'status' => File::exists(config_path('menu.php')),
                'label' => 'Menu Config (config/menu.php)',
                'message' => File::exists(config_path('menu.php'))
                    ? 'Found and ready for integration'
                    : 'config/menu.php not found',
            ],
            'routes_modular' => [
                'status' => File::isDirectory(base_path('routes/web')),
                'label' => 'Modular Routes (routes/web/)',
                'message' => File::isDirectory(base_path('routes/web'))
                    ? 'Directory exists for isolated module routes'
                    : 'routes/web/ will be created automatically',
            ],
            'localization' => [
                'status' => File::isDirectory(base_path('lang')),
                'label' => 'Localization (lang/)',
                'message' => File::isDirectory(base_path('lang'))
                    ? 'Multi-language directory ready'
                    : 'lang/ directory not found',
            ],
        ];
    }

    /**
     * Check if all required environment criteria are met.
     */
    public function isReady(): bool
    {
        $inspections = $this->inspect();

        return $inspections['php_version']['status'] && $inspections['laravel_version']['status'];
    }
}
