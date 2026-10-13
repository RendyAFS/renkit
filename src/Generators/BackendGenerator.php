<?php

namespace RendyAFS\RenKit\Generators;

use Illuminate\Support\Facades\File;
use RendyAFS\RenKit\Support\StubCompiler;

class BackendGenerator
{
    public function __construct(
        protected StubCompiler $compiler
    ) {}

    /**
     * Generate all backend files for the module.
     *
     * @param  array<string, string>  $vars
     * @param  string  $mode  'simple' or 'resource'
     * @param  bool  $force
     * @param  array<string, string>  $schema
     * @return array<string, string> Created files list
     */
    public function generate(array $vars, string $mode = 'simple', bool $force = false, array $schema = []): array
    {
        $vars = array_merge($vars, $schema);

        $vars['modelFillable'] ??= "[\n        'name',\n        'description',\n        'is_active',\n    ]";
        $vars['modelCasts'] ??= "[\n            'is_active' => 'boolean',\n        ]";
        $vars['validationRules'] ??= "[\n            'name'        => ['required', 'string', 'max:255'],\n            'description' => ['nullable', 'string'],\n            'is_active'   => ['nullable', 'boolean'],\n        ]";

        $created = [];

        // 1. Model
        $modelPath = app_path("Models/{$vars['studly']}.php");
        $modelContent = $this->compiler->compile($this->compiler->getStubContent('model.stub'), $vars);
        $this->saveFile($modelPath, $modelContent, $force);
        $created['Model'] = $modelPath;

        // 2. Migration
        $migrationName = "create_{$vars['tableName']}_table";
        $existingMigration = $this->findExistingMigration($vars['tableName']);
        if (! $existingMigration || $force) {
            $migrationFileName = date('Y_m_d_His')."_{$migrationName}.php";
            $migrationPath = database_path("migrations/{$migrationFileName}");
            $migrationContent = $this->compiler->compile($this->compiler->getStubContent('migration.stub'), $vars);
            $this->saveFile($migrationPath, $migrationContent, $force);
            $created['Migration'] = $migrationPath;
        } else {
            $created['Migration (Existing)'] = $existingMigration;
        }

        // 3. Controller
        $controllerStub = $mode === 'resource' ? 'controller.resource.stub' : 'controller.simple.stub';
        $controllerPath = app_path("Http/Controllers/{$vars['studly']}Controller.php");
        $controllerContent = $this->compiler->compile($this->compiler->getStubContent($controllerStub), $vars);
        $this->saveFile($controllerPath, $controllerContent, $force);
        $created['Controller'] = $controllerPath;

        // 4. Repository
        $repoPath = app_path("Repositories/{$vars['studly']}Repository.php");
        $repoContent = $this->compiler->compile($this->compiler->getStubContent('repository.stub'), $vars);
        $this->saveFile($repoPath, $repoContent, $force);
        $created['Repository'] = $repoPath;

        // 5. Form Request
        $requestPath = app_path("Http/Requests/{$vars['studly']}/Save{$vars['studly']}Request.php");
        $requestContent = $this->compiler->compile($this->compiler->getStubContent('request.stub'), $vars);
        $this->saveFile($requestPath, $requestContent, $force);
        $created['Request'] = $requestPath;

        // 6. Resource
        $resourcePath = app_path("Http/Resources/{$vars['studly']}Resource.php");
        $resourceContent = $this->compiler->compile($this->compiler->getStubContent('resource.stub'), $vars);
        $this->saveFile($resourcePath, $resourceContent, $force);
        $created['Resource'] = $resourcePath;

        // 7. Action (if resource mode)
        if ($mode === 'resource') {
            $actionPath = app_path("Actions/{$vars['studly']}/Save{$vars['studly']}Action.php");
            $actionContent = $this->compiler->compile($this->compiler->getStubContent('action.stub'), $vars);
            $this->saveFile($actionPath, $actionContent, $force);
            $created['Action'] = $actionPath;
        }

        return $created;
    }

    protected function saveFile(string $path, string $content, bool $force = false): void
    {
        File::ensureDirectoryExists(dirname($path));

        if (! File::exists($path) || $force) {
            File::put($path, $content);
        }
    }

    protected function findExistingMigration(string $tableName): ?string
    {
        $files = File::glob(database_path("migrations/*_create_{$tableName}_table.php"));
        if (! empty($files)) {
            return $files[0];
        }

        $fallbackFiles = File::glob(database_path("migrations/*_{$tableName}_table.php"));
        if (! empty($fallbackFiles)) {
            return $fallbackFiles[0];
        }

        $fallbackWildcard = File::glob(database_path("migrations/*_{$tableName}*.php"));
        return ! empty($fallbackWildcard) ? $fallbackWildcard[0] : null;
    }
}
