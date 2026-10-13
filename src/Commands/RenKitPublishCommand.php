<?php

namespace RendyAFS\RenKit\Commands;

use Illuminate\Console\Command;
use Illuminate\Support\Facades\File;

class RenKitPublishCommand extends Command
{
    protected $signature = 'renkit:publish
        {--theme : Publish RenKit CSS theme colors (:root variables) & loading CSS}
        {--layouts : Publish Master layouts (layouts/main.blade.php & layouts/lang.blade.php)}
        {--components : Publish Blade UI components (sidebar, navbar, modal, alert, datatable)}
        {--utils : Publish JavaScript utilities (api-provider, button-loading, alert, datatable)}
        {--vendor-js : Publish offline vendor JavaScript libraries (jquery, blockUi)}
        {--auth : Publish Authentication views & Fortify scaffolding}
        {--stubs : Publish customizable module generator stubs}
        {--lang : Publish localization language files (lang/id and lang/en)}
        {--all : Publish all RenKit customizable assets}
        {--force : Overwrite existing files}
    ';

    protected $description = 'Publish RenKit customizable theme colors, Blade components, JS utilities, stubs, and localization';

    public function handle(): int
    {
        $this->newLine();
        $this->components->info('🎨 RenKit Asset Publisher');
        $this->line(str_repeat('─', 60));

        $force = (bool) $this->option('force');
        $pubAll = (bool) $this->option('all');
        $pubTheme = $pubAll || (bool) $this->option('theme');
        $pubLayouts = $pubAll || (bool) $this->option('layouts');
        $pubComponents = $pubAll || (bool) $this->option('components');
        $pubUtils = $pubAll || (bool) $this->option('utils');
        $pubVendorJs = $pubAll || (bool) $this->option('vendor-js');
        $pubAuth = $pubAll || (bool) $this->option('auth');
        $pubStubs = $pubAll || (bool) $this->option('stubs');
        $pubLang = $pubAll || (bool) $this->option('lang');

        // If no flags are provided, show an interactive choice
        if (! $pubTheme && ! $pubLayouts && ! $pubComponents && ! $pubUtils && ! $pubVendorJs && ! $pubAuth && ! $pubStubs && ! $pubLang) {
            $selected = $this->choice(
                'What assets would you like to publish?',
                [
                    'theme' => 'Theme CSS (:root color palette, loading.css, and Preline styling)',
                    'layouts' => 'Master Layouts (main.blade.php & lang.blade.php)',
                    'components' => 'Blade Components (<x-sidebar>, <x-navbar>, <x-modal>, <x-datatable>)',
                    'utils' => 'JavaScript Utilities (api-provider.js, datatable.js, alert, loading)',
                    'vendor-js' => 'Offline Vendor JavaScript (jquery-3.6.0.min.js, blockUi.js)',
                    'auth' => 'Authentication Views & Fortify Scaffolding',
                    'stubs' => 'CRUD Generator Stubs (stubs/renkit/)',
                    'lang' => 'Localization Language Files (lang/id and lang/en)',
                    'all' => 'Everything (Theme, Layouts, Components, JS, Vendor JS, Auth, Stubs, Lang)',
                ],
                'all'
            );

            if ($selected === 'all') {
                $pubTheme = $pubLayouts = $pubComponents = $pubUtils = $pubVendorJs = $pubAuth = $pubStubs = $pubLang = true;
            } else {
                $optName = 'pub'.str_replace('-', '', ucwords($selected, '-'));
                $$optName = true;
            }
        }

        $published = [];

        // 1. Theme CSS
        if ($pubTheme) {
            $this->components->task('Publishing Theme CSS (resources/css/)', function () use ($force, &$published) {
                $cssSource = __DIR__.'/../../resources/css';
                $cssTarget = resource_path('css');
                File::ensureDirectoryExists($cssTarget);
                File::copyDirectory($cssSource, $cssTarget);
                $published[] = ['Theme CSS', 'resources/css/'];

                return true;
            });
        }

        // 2. Layouts
        if ($pubLayouts) {
            $this->components->task('Publishing Master Layouts (resources/views/layouts/)', function () use ($force, &$published) {
                $layoutSource = __DIR__.'/../../resources/views/layouts';
                $layoutTarget = resource_path('views/layouts');
                File::ensureDirectoryExists($layoutTarget);
                File::copyDirectory($layoutSource, $layoutTarget);
                $published[] = ['Master Layouts', 'resources/views/layouts/'];

                return true;
            });
        }

        // 3. Blade Components
        if ($pubComponents) {
            $this->components->task('Publishing Blade Components (resources/views/components/)', function () use ($force, &$published) {
                $compSource = __DIR__.'/../../resources/views/components';
                $compTarget = resource_path('views/components');
                File::ensureDirectoryExists($compTarget);
                File::copyDirectory($compSource, $compTarget);
                $published[] = ['Blade Components', 'resources/views/components/'];

                return true;
            });
        }

        // 4. JavaScript Utilities
        if ($pubUtils) {
            $this->components->task('Publishing JS Utilities & App Entry (resources/js/)', function () use ($force, &$published) {
                $utilSource = __DIR__.'/../../resources/js/utils';
                $utilTarget = resource_path('js/utils');
                File::ensureDirectoryExists($utilTarget);
                File::copyDirectory($utilSource, $utilTarget);

                $appJsSource = __DIR__.'/../../resources/js/app.js';
                $appJsTarget = resource_path('js/app.js');
                if (File::exists($appJsSource) && (! File::exists($appJsTarget) || $force)) {
                    File::put($appJsTarget, File::get($appJsSource));
                }

                $published[] = ['JS Utilities', 'resources/js/utils/'];

                return true;
            });
        }

        // 5. Offline Vendor JS
        if ($pubVendorJs) {
            $this->components->task('Publishing Offline Vendor JS (public/js/)', function () use ($force, &$published) {
                $vendorSource = __DIR__.'/../../public/js';
                $vendorTarget = public_path('js');
                File::ensureDirectoryExists($vendorTarget);
                File::copyDirectory($vendorSource, $vendorTarget);
                $published[] = ['Vendor JS', 'public/js/'];

                return true;
            });
        }

        // 6. Auth Views
        if ($pubAuth) {
            $this->components->task('Publishing Authentication Views (resources/views/auth/)', function () use ($force, &$published) {
                $authSource = __DIR__.'/../../resources/views/auth';
                $authTarget = resource_path('views/auth');
                File::ensureDirectoryExists($authTarget);
                File::copyDirectory($authSource, $authTarget);
                $published[] = ['Auth Views', 'resources/views/auth/'];

                return true;
            });
        }

        // 7. Stubs
        if ($pubStubs) {
            $this->components->task('Publishing Generator Stubs (stubs/renkit/)', function () use (&$published) {
                $stubSource = __DIR__.'/../../stubs';
                $stubTarget = base_path('stubs/renkit');
                File::ensureDirectoryExists($stubTarget);
                File::copyDirectory($stubSource, $stubTarget);
                $published[] = ['Stubs', 'stubs/renkit/'];

                return true;
            });
        }

        // 8. Localization
        if ($pubLang) {
            $this->components->task('Publishing Localization (lang/id & lang/en)', function () use ($force, &$published) {
                $langSource = __DIR__.'/../../resources/lang';
                $langTarget = base_path('lang');
                File::ensureDirectoryExists($langTarget);
                File::copyDirectory($langSource, $langTarget);
                $published[] = ['Localization', 'lang/'];

                return true;
            });
        }

        $this->newLine();
        $this->components->info('🎉 RenKit assets published successfully!');
        if (! empty($published)) {
            $this->table(['Type', 'Target Location'], $published);
        }

        $this->newLine();

        return self::SUCCESS;
    }
}
