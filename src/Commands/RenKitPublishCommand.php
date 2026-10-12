<?php

namespace RendyAFS\RenKit\Commands;

use Illuminate\Console\Command;
use Illuminate\Support\Facades\File;

class RenKitPublishCommand extends Command
{
    protected $signature = 'renkit:publish
        {--theme : Publish RenKit CSS theme colors (:root variables) & loading CSS}
        {--components : Publish Blade UI components (modal, button-loading, alert, datatable)}
        {--utils : Publish JavaScript utilities (api-provider, button-loading, alert, loading)}
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
        $pubComponents = $pubAll || (bool) $this->option('components');
        $pubUtils = $pubAll || (bool) $this->option('utils');
        $pubStubs = $pubAll || (bool) $this->option('stubs');
        $pubLang = $pubAll || (bool) $this->option('lang');

        // If no flags are provided, show an interactive choice
        if (! $pubTheme && ! $pubComponents && ! $pubUtils && ! $pubStubs && ! $pubLang) {
            $selected = $this->choice(
                'What assets would you like to publish?',
                [
                    'theme' => 'Theme CSS (:root color palette & loading.css)',
                    'components' => 'Blade Components (<x-modal>, <x-button-loading>, <x-custom-alert>, <x-datatable>, <x-cardgrid>)',
                    'utils' => 'JavaScript Utilities (api-provider.js, button-loading.js, alert, loading, cardgrid)',
                    'stubs' => 'CRUD Generator Stubs (stubs/renkit/)',
                    'lang' => 'Localization Language Files (lang/id and lang/en)',
                    'all' => 'Everything (Theme, Components, JS Utilities, Stubs, Lang)',
                ],
                'all'
            );

            if ($selected === 'all') {
                $pubTheme = $pubComponents = $pubUtils = $pubStubs = $pubLang = true;
            } else {
                ${'pub'.ucfirst($selected)} = true;
            }
        }

        $published = [];

        // 1. Theme CSS
        if ($pubTheme) {
            $this->components->task('Publishing Theme CSS (resources/css/)', function () use ($force, &$published) {
                $cssSource = __DIR__.'/../../resources/css';
                $cssTarget = resource_path('css');
                File::ensureDirectoryExists($cssTarget);

                foreach (File::files($cssSource) as $file) {
                    $dest = $cssTarget.'/'.$file->getFilename();
                    if (! File::exists($dest) || $force) {
                        File::copy($file->getPathname(), $dest);
                        $published[] = ['Theme CSS', 'resources/css/'.$file->getFilename()];
                    }
                }

                return true;
            });
        }

        // 2. Blade Components
        if ($pubComponents) {
            $this->components->task('Publishing Blade Components (resources/views/components/)', function () use ($force, &$published) {
                $compSource = __DIR__.'/../../resources/views/components';
                $compTarget = resource_path('views/components');
                File::ensureDirectoryExists($compTarget);

                foreach (File::files($compSource) as $file) {
                    $dest = $compTarget.'/'.$file->getFilename();
                    if (! File::exists($dest) || $force) {
                        File::copy($file->getPathname(), $dest);
                        $published[] = ['Blade Component', 'resources/views/components/'.$file->getFilename()];
                    }
                }

                return true;
            });
        }

        // 3. JavaScript Utilities
        if ($pubUtils) {
            $this->components->task('Publishing JS Utilities (resources/js/utils/)', function () use ($force, &$published) {
                $utilSource = __DIR__.'/../../resources/js/utils';
                $utilTarget = resource_path('js/utils');
                File::ensureDirectoryExists($utilTarget);

                foreach (File::files($utilSource) as $file) {
                    $dest = $utilTarget.'/'.$file->getFilename();
                    if (! File::exists($dest) || $force) {
                        File::copy($file->getPathname(), $dest);
                        $published[] = ['JS Utility', 'resources/js/utils/'.$file->getFilename()];
                    }
                }

                return true;
            });
        }

        // 4. Stubs
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

        // 5. Localization
        if ($pubLang) {
            $this->components->task('Publishing Localization (lang/id & lang/en)', function () use ($force, &$published) {
                $langSource = __DIR__.'/../../resources/lang';
                $langTarget = base_path('lang');
                File::ensureDirectoryExists($langTarget);

                foreach (['id', 'en'] as $locale) {
                    $srcLocaleDir = $langSource.'/'.$locale;
                    $tgtLocaleDir = $langTarget.'/'.$locale;
                    File::ensureDirectoryExists($tgtLocaleDir);

                    if (File::isDirectory($srcLocaleDir)) {
                        foreach (File::files($srcLocaleDir) as $file) {
                            $dest = $tgtLocaleDir.'/'.$file->getFilename();
                            if (! File::exists($dest) || $force) {
                                File::copy($file->getPathname(), $dest);
                                $published[] = ['Localization', "lang/{$locale}/".$file->getFilename()];
                            }
                        }
                    }
                }

                return true;
            });
        }

        $this->newLine();
        $this->components->info('🎉 RenKit assets published successfully!');
        if (! empty($published)) {
            $this->table(['Type', 'Target Location'], $published);
        }

        $this->newLine();
        $this->components->warn('Tips:');
        $this->line(' • Edit <fg=cyan>resources/css/theme.css</> to customize your brand colors (:root variables).');
        $this->line(' • In Tailwind CSS v4, your theme variables are available as: <fg=cyan>bg-(--color-primary)</>, <fg=cyan>text-(--color-dark)</>, etc.');
        $this->line(' • Ensure <fg=cyan>resources/css/app.css</> imports your theme: <fg=cyan>@import "./theme.css";</>');
        $this->newLine();

        return self::SUCCESS;
    }
}
