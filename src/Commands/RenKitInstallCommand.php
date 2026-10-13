<?php

namespace RendyAFS\RenKit\Commands;

use Illuminate\Console\Command;
use Illuminate\Support\Facades\File;
use RendyAFS\RenKit\Support\AuthScaffoldSetup;
use RendyAFS\RenKit\Support\EnvironmentDetector;
use RendyAFS\RenKit\Support\FrontendToolchainSetup;
use RendyAFS\RenKit\Support\LayoutScaffoldSetup;

class RenKitInstallCommand extends Command
{
    protected $signature = 'renkit:install
        {--force : Overwrite existing configuration}
        {--stubs : Publish editable stubs to stubs/renkit}
        {--toolchain : Automatically setup Frontend Toolchain (Tailwind v4, Preline, Vite)}
        {--no-toolchain : Skip Frontend Toolchain setup}
        {--auth : Setup Authentication scaffolding (Fortify + Preline UI)}
        {--no-auth : Skip Authentication scaffolding}
        {--lang : Enable multi-language localization (publish lang files)}
        {--no-lang : Disable multi-language localization}
    ';

    protected $description = 'Install and configure RenKit development toolkit';

    public function handle(
        EnvironmentDetector $detector,
        FrontendToolchainSetup $toolchainSetup,
        AuthScaffoldSetup $authSetup,
        LayoutScaffoldSetup $layoutSetup
    ): int {
        $this->newLine();
        $this->components->info('🚀 Installing RenKit Development Toolkit');
        $this->line(str_repeat('─', 60));

        // 1. Diagnose Environment Readiness
        $this->components->task('Checking Environment Readiness', function () use ($detector) {
            return $detector->isReady();
        });

        // 2. Frontend Toolchain (Preline UI, Tailwind v4, Vite & JS utils)
        $enableToolchain = true;
        if ($this->option('no-toolchain')) {
            $enableToolchain = false;
        } elseif ($this->option('toolchain')) {
            $enableToolchain = true;
        } elseif ($this->input->isInteractive()) {
            $enableToolchain = $this->confirm(
                'Would you like to setup Frontend Toolchain & Preline UI (Tailwind CSS v4, Vite, Preline)?',
                true
            );
        }

        if ($enableToolchain) {
            $this->components->task('Configuring Frontend Toolchain & Preline UI', function () use ($toolchainSetup) {
                $toolchainSetup->setup();
                return true;
            });
        }

        // 3. Optional Authentication Scaffolding (Fortify + Preline UI Views)
        $enableAuth = true;
        if ($this->option('no-auth')) {
            $enableAuth = false;
        } elseif ($this->option('auth')) {
            $enableAuth = true;
        } elseif ($this->input->isInteractive()) {
            $enableAuth = $this->confirm(
                'Would you like to install Authentication scaffolding (Fortify + Preline UI Login/Register)?',
                true
            );
        }

        if ($enableAuth) {
            $this->components->task('Scaffolding Authentication Views & Fortify Provider', function () use ($authSetup) {
                $authSetup->setup();
                return true;
            });
        }

        // 4. Base Layout, Sidebar, Navbar & Menu Shell
        $this->components->task('Publishing Master Layout, Navbar, Sidebar & Menu Shell', function () use ($layoutSetup) {
            $layoutSetup->setup();
            return true;
        });

        // 5. Decide Localization Support
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

        // 6. Publish or generate config/renkit.php
        $configPath = config_path('renkit.php');
        $alreadyConfigured = File::exists($configPath);

        if (! $alreadyConfigured || $this->option('force')) {
            $this->components->task('Publishing config/renkit.php', function () use ($configPath, $enableLang, $enableAuth) {
                $sourceConfig = __DIR__.'/../../config/renkit.php';
                File::ensureDirectoryExists(dirname($configPath));
                $content = File::get($sourceConfig);

                if (! $enableLang) {
                    $content = str_replace(
                        "'localization' => env('RENKIT_LOCALIZATION', true)",
                        "'localization' => env('RENKIT_LOCALIZATION', false)",
                        $content
                    );
                    $content = str_replace(
                        "'default_locale' => env('RENKIT_LOCALE', 'id')",
                        "'default_locale' => env('RENKIT_LOCALE', 'en')",
                        $content
                    );

                    // Ensure APP_LOCALE=en in .env
                    $envPath = base_path('.env');
                    if (File::exists($envPath)) {
                        $envContent = File::get($envPath);
                        if (str_contains($envContent, 'APP_LOCALE=')) {
                            $envContent = preg_replace('/APP_LOCALE=[a-zA-Z_-]+/', 'APP_LOCALE=en', $envContent);
                            File::put($envPath, $envContent);
                        }
                    }
                }

                File::put($configPath, $content);

                return true;
            });
        } else {
            $this->components->twoColumnDetail('Configuration', '<fg=yellow>Existing config/renkit.php kept</>');
        }

        // 7. Publish Localization if enabled
        if ($enableLang) {
            $this->components->task('Publishing Starter Localization (lang/id & lang/en)', function () {
                $this->callSilent('renkit:publish', ['--lang' => true, '--force' => (bool) $this->option('force')]);

                return true;
            });
        }

        // 8. Optional Stubs Publication
        if ($this->option('stubs')) {
            $this->components->task('Publishing Customizable Stubs', function () {
                $targetStubs = base_path('stubs/renkit');
                $sourceStubs = __DIR__.'/../../stubs';
                File::ensureDirectoryExists($targetStubs);
                File::copyDirectory($sourceStubs, $targetStubs);

                return true;
            });
        }

        // 9. Ensure directories exist
        $this->components->task('Ensuring Modular Route & JS Directories', function () {
            File::ensureDirectoryExists(base_path('routes/web'));
            File::ensureDirectoryExists(resource_path('js/pages'));

            return true;
        });

        $this->newLine();
        $this->components->info('✅ RenKit installed and configured successfully!');
        $this->line('  Run <fg=cyan>npm install && npm run build</> to compile frontend assets.');
        $this->line('  Run <fg=cyan>php artisan renkit:about</> to view active features.');
        $this->line('  Run <fg=cyan>php artisan renkit:module {Name} [--simple|--resource]</> to generate your first module.');
        $this->newLine();

        return self::SUCCESS;
    }
}
