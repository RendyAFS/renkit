<?php

namespace RendyAFS\RenKit\Commands;

use Illuminate\Console\Command;
use Illuminate\Support\Facades\File;
use RendyAFS\RenKit\Support\EnvironmentDetector;

class RenKitInstallCommand extends Command
{
    protected $signature = 'renkit:install
        {--force : Overwrite existing configuration}
        {--stubs : Publish editable stubs to stubs/renkit}
        {--lang : Enable multi-language localization (publish lang files)}
        {--no-lang : Disable multi-language localization}
    ';

    protected $description = 'Install and configure RenKit development toolkit';

    public function handle(EnvironmentDetector $detector): int
    {
        $this->newLine();
        $this->components->info('🚀 Installing RenKit Development Toolkit');
        $this->line(str_repeat('─', 60));

        // 1. Diagnose Environment
        $this->components->task('Checking Environment Readiness', function () use ($detector) {
            return $detector->isReady();
        });

        // 2. Decide Localization Support
        $enableLang = true;
        if ($this->option('no-lang')) {
            $enableLang = false;
        } elseif ($this->option('lang')) {
            $enableLang = true;
        } elseif ($this->input->isInteractive()) {
            $enableLang = $this->confirm(
                'Would you like to enable Multi-Language Localization (lang/id and lang/en)?',
                true
            );
        }

        // 3. Publish or generate config/renkit.php
        $configPath = config_path('renkit.php');
        $alreadyConfigured = File::exists($configPath);

        if (! $alreadyConfigured || $this->option('force')) {
            $this->components->task('Publishing config/renkit.php', function () use ($configPath, $enableLang) {
                $sourceConfig = __DIR__.'/../../config/renkit.php';
                File::ensureDirectoryExists(dirname($configPath));
                $content = File::get($sourceConfig);

                if (! $enableLang) {
                    $content = str_replace(
                        "'localization' => env('RENKIT_LOCALIZATION', true)",
                        "'localization' => env('RENKIT_LOCALIZATION', false)",
                        $content
                    );
                }

                File::put($configPath, $content);

                return true;
            });
        } else {
            $this->components->twoColumnDetail('Configuration', '<fg=yellow>Existing config/renkit.php kept</>');
        }

        // 4. Publish Localization if enabled
        if ($enableLang) {
            $this->components->task('Publishing Starter Localization (lang/id & lang/en)', function () {
                $this->callSilent('renkit:publish', ['--lang' => true, '--force' => (bool) $this->option('force')]);

                return true;
            });
        }

        // 5. Optional Stubs Publication
        if ($this->option('stubs')) {
            $this->components->task('Publishing Customizable Stubs', function () {
                $targetStubs = base_path('stubs/renkit');
                $sourceStubs = __DIR__.'/../../stubs';
                File::ensureDirectoryExists($targetStubs);
                File::copyDirectory($sourceStubs, $targetStubs);

                return true;
            });
        }

        // 6. Ensure directories exist
        $this->components->task('Ensuring Modular Route & JS Directories', function () {
            File::ensureDirectoryExists(base_path('routes/web'));
            File::ensureDirectoryExists(resource_path('js/pages'));

            return true;
        });

        $this->newLine();
        $this->components->info('✅ RenKit installed and configured successfully!');
        $this->line('  Run <fg=cyan>php artisan renkit:about</> to view active features.');
        $this->line('  Run <fg=cyan>php artisan renkit:module {Name} [--simple|--resource]</> to generate your first module.');
        $this->newLine();

        return self::SUCCESS;
    }
}
