<?php

namespace RendyAFS\RenKit;

use Illuminate\Support\ServiceProvider;
use RendyAFS\RenKit\Commands\RenKitAboutCommand;
use RendyAFS\RenKit\Commands\RenKitDoctorCommand;
use RendyAFS\RenKit\Commands\RenKitInstallCommand;
use RendyAFS\RenKit\Commands\RenKitModuleCommand;
use RendyAFS\RenKit\Commands\RenKitPublishCommand;
use RendyAFS\RenKit\Generators\BackendGenerator;
use RendyAFS\RenKit\Generators\LangIntegrator;
use RendyAFS\RenKit\Generators\MenuIntegrator;
use RendyAFS\RenKit\Generators\RouteIntegrator;
use RendyAFS\RenKit\Support\EnvironmentDetector;
use RendyAFS\RenKit\Support\StubCompiler;

class RenKitServiceProvider extends ServiceProvider
{
    /**
     * Register any application services.
     */
    public function register(): void
    {
        // Merge package configuration
        $this->mergeConfigFrom(
            __DIR__.'/../config/renkit.php',
            'renkit'
        );

        // Bind main manager
        $this->app->singleton('renkit', function () {
            return new RenKit;
        });
        $this->app->alias('renkit', RenKit::class);

        // Bind singletons & generators
        $this->app->singleton(StubCompiler::class);
        $this->app->singleton(EnvironmentDetector::class);
        $this->app->singleton(BackendGenerator::class);
        $this->app->singleton(FrontendGenerator::class);
        $this->app->singleton(RouteIntegrator::class);
        $this->app->singleton(MenuIntegrator::class);
        $this->app->singleton(LangIntegrator::class);
    }

    /**
     * Bootstrap any application services.
     */
    public function boot(): void
    {
        // Load package translations (renkit::)
        $this->loadTranslationsFrom(__DIR__.'/../resources/lang', 'renkit');

        // Register console commands
        if ($this->app->runningInConsole()) {
            $this->commands([
                RenKitInstallCommand::class,
                RenKitModuleCommand::class,
                RenKitAboutCommand::class,
                RenKitDoctorCommand::class,
                RenKitPublishCommand::class,
            ]);

            // Publish configuration
            $this->publishes([
                __DIR__.'/../config/renkit.php' => config_path('renkit.php'),
            ], 'renkit-config');

            // Publish customizable stubs
            $this->publishes([
                __DIR__.'/../stubs' => base_path('stubs/renkit'),
            ], 'renkit-stubs');

            // Publish theme CSS
            $this->publishes([
                __DIR__.'/../resources/css' => resource_path('css'),
            ], 'renkit-theme');

            // Publish Blade components
            $this->publishes([
                __DIR__.'/../resources/views/components' => resource_path('views/components'),
            ], 'renkit-components');

            // Publish JS utilities
            $this->publishes([
                __DIR__.'/../resources/js/utils' => resource_path('js/utils'),
            ], 'renkit-utils');

            // Publish Localization (lang/id and lang/en)
            $this->publishes([
                __DIR__.'/../resources/lang' => base_path('lang'),
            ], 'renkit-lang');

            // Publish everything
            $this->publishes([
                __DIR__.'/../config/renkit.php' => config_path('renkit.php'),
                __DIR__.'/../stubs' => base_path('stubs/renkit'),
                __DIR__.'/../resources/css' => resource_path('css'),
                __DIR__.'/../resources/views/components' => resource_path('views/components'),
                __DIR__.'/../resources/js/utils' => resource_path('js/utils'),
                __DIR__.'/../resources/lang' => base_path('lang'),
            ], 'renkit-assets');
        }
    }
}
