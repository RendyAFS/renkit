<?php

namespace RendyAFS\RenKit\Commands;

use Illuminate\Console\Command;
use RendyAFS\RenKit\Generators\BackendGenerator;
use RendyAFS\RenKit\Generators\FrontendGenerator;
use RendyAFS\RenKit\Generators\LangIntegrator;
use RendyAFS\RenKit\Generators\MenuIntegrator;
use RendyAFS\RenKit\Generators\RouteIntegrator;
use RendyAFS\RenKit\Support\StubCompiler;

class RenKitModuleCommand extends Command
{
    protected $signature = 'renkit:module
        {name : The name of the module (e.g. Category, Role, Product)}
        {--simple : Generate simple modal-based CRUD}
        {--resource : Generate full page CRUD (with Action & separate views)}
        {--cardgrid : Generate CardGrid UI instead of DataTable}
        {--datatable : Generate DataTable UI (default)}
        {--force : Overwrite existing files}
        {--no-menu : Do not register in config/menu.php}
        {--no-lang : Do not generate or register translation keys}
    ';

    protected $description = 'Generate a complete, ready-to-use CRUD module';

    public function handle(
        StubCompiler $compiler,
        BackendGenerator $backendGen,
        FrontendGenerator $frontendGen,
        RouteIntegrator $routeIntegrator,
        MenuIntegrator $menuIntegrator,
        LangIntegrator $langIntegrator
    ): int {
        $rawName = trim($this->argument('name'));

        if (empty($rawName)) {
            $this->components->error('Module name cannot be empty.');

            return self::FAILURE;
        }

        $isSimple = (bool) $this->option('simple');
        $isResource = (bool) $this->option('resource');
        $isCardgrid = (bool) $this->option('cardgrid');
        $isDatatable = (bool) $this->option('datatable');
        $force = (bool) $this->option('force');

        // 1. Determine CRUD Mode (Simple modal vs Resource full-page)
        $mode = 'simple';
        $promptedForMode = false;
        if ($isResource) {
            $mode = 'resource';
        } elseif ($isSimple) {
            $mode = 'simple';
        } elseif ($this->input->isInteractive()) {
            $promptedForMode = true;
            $mode = $this->choice(
                'Select CRUD Architecture mode:',
                [
                    'simple' => 'Simple Modal CRUD (Modal overlay + form)',
                    'resource' => 'Full Page CRUD (Separate create/edit pages + Action class)',
                ],
                config('renkit.generator.default_mode', 'simple')
            );
        } else {
            $mode = config('renkit.generator.default_mode', 'simple');
        }

        // 2. Determine View UI Layout (DataTable vs CardGrid)
        $viewType = 'datatable';
        if ($isCardgrid) {
            $viewType = 'cardgrid';
        } elseif ($isDatatable) {
            $viewType = 'datatable';
        } elseif ($promptedForMode && $this->input->isInteractive()) {
            $viewType = $this->choice(
                'Select View UI Layout:',
                [
                    'datatable' => 'DataTable (<x-datatable>, Tabular list with live search & filters)',
                    'cardgrid' => 'CardGrid (<x-cardgrid>, Responsive cards with live search & filters)',
                ],
                'datatable'
            );
        } else {
            $viewType = 'datatable';
        }

        $vars = $compiler->getModuleVariables($rawName);

        $this->newLine();
        $this->components->info("🚀 Generating RenKit Module: {$vars['studly']}");
        $this->line(str_repeat('─', 60));

        $this->components->twoColumnDetail('Module Name', $vars['studly']);
        $this->components->twoColumnDetail('CRUD Type', $mode === 'resource' ? 'Full Page CRUD (--resource)' : 'Simple Modal CRUD (--simple)');
        $this->components->twoColumnDetail('View Layout', $viewType === 'cardgrid' ? 'CardGrid (<x-cardgrid>)' : 'DataTable (<x-datatable>)');
        $this->components->twoColumnDetail('Target Route', "/{$vars['routePrefix']}");
        $this->components->twoColumnDetail('Target Table', $vars['tableName']);
        $this->newLine();

        $generatedFiles = [];

        // 1. Generate Backend Files
        $this->components->task('Generating Backend Architecture', function () use ($backendGen, $vars, $mode, $force, &$generatedFiles) {
            $backendFiles = $backendGen->generate($vars, $mode, $force);
            foreach ($backendFiles as $type => $path) {
                $generatedFiles[] = [$type, str_replace(base_path().DIRECTORY_SEPARATOR, '', $path)];
            }

            return true;
        });

        // 2. Generate Frontend Files (Blade & JS)
        $this->components->task('Generating Frontend Views & Scripts', function () use ($frontendGen, $vars, $mode, $viewType, $force, &$generatedFiles) {
            $frontendFiles = $frontendGen->generate($vars, $mode, $viewType, $force);
            foreach ($frontendFiles as $type => $path) {
                $generatedFiles[] = [$type, str_replace(base_path().DIRECTORY_SEPARATOR, '', $path)];
            }

            return true;
        });

        // 3. Register Modular Route & Include in web.php
        $this->components->task('Registering Route (routes/web/'.$vars['pluralKebab'].'.php)', function () use ($routeIntegrator, $vars, $force, &$generatedFiles) {
            $routeResult = $routeIntegrator->integrate($vars, $force);
            $generatedFiles[] = ['Route', str_replace(base_path().DIRECTORY_SEPARATOR, '', $routeResult['created_file'])];

            return true;
        });

        // 4. Menu Integration
        if (! $this->option('no-menu') && config('renkit.features.menu', true)) {
            $this->components->task('Integrating into config/menu.php', function () use ($menuIntegrator, $vars) {
                $menuResult = $menuIntegrator->integrate($vars);

                return $menuResult['status'];
            });
        }

        // 5. Localization Integration
        if (! $this->option('no-lang') && config('renkit.features.localization', true)) {
            $this->components->task('Integrating Localization (lang/id & lang/en)', function () use ($langIntegrator, $vars, $force, &$generatedFiles) {
                $langResult = $langIntegrator->integrate($vars, $force);
                foreach ($langResult['created_files'] as $createdLang) {
                    $generatedFiles[] = ['Lang', str_replace(base_path().DIRECTORY_SEPARATOR, '', $createdLang)];
                }

                return $langResult['status'];
            });
        }

        $this->newLine();
        $this->components->info("🎉 Module {$vars['studly']} created successfully!");
        $this->newLine();

        $this->table(
            ['Component', 'Relative Path'],
            $generatedFiles
        );

        $this->newLine();
        $this->components->warn('Next steps:');
        $this->line(' 1. Run migration: <fg=cyan>php artisan migrate</>');
        $this->line(' 2. Seed menu permissions (if Spatie active): <fg=cyan>php artisan db:seed --class=MenuPermissionSeeder</>');
        $this->line(' 3. Visit route in browser: <fg=cyan>'.url('/'.$vars['routePrefix']).'</>');
        $this->newLine();

        return self::SUCCESS;
    }
}
