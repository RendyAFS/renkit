<?php

namespace RendyAFS\RenKit\Support;

use Illuminate\Support\Facades\File;
use Illuminate\Support\Str;

class StubCompiler
{
    /**
     * Compile a stub string by replacing placeholders with module variables.
     *
     * @param  array<string, string>  $variables
     */
    public function compile(string $stubContent, array $variables = []): string
    {
        foreach ($variables as $key => $value) {
            $stubContent = str_replace('{{'.$key.'}}', (string) $value, $stubContent);
            $stubContent = str_replace('{{ '.$key.' }}', (string) $value, $stubContent);
        }

        return $stubContent;
    }

    /**
     * Get variables map for a given module name.
     *
     * @return array<string, string>
     */
    public function getModuleVariables(string $name): array
    {
        $singularStudly = Str::studly(Str::singular($name));
        $singularKebab = Str::kebab(Str::singular($name));
        $singularCamel = Str::camel(Str::singular($name));
        $singularSnake = Str::snake(Str::singular($name));

        $pluralStudly = Str::studly(Str::plural($name));
        $pluralKebab = Str::kebab(Str::plural($name));
        $pluralCamel = Str::camel(Str::plural($name));
        $pluralSnake = Str::snake(Str::plural($name));

        $title = Str::headline($singularStudly);
        $pluralTitle = Str::headline($pluralStudly);

        return [
            'name' => $singularStudly,
            'studly' => $singularStudly,
            'camel' => $singularCamel,
            'kebab' => $singularKebab,
            'snake' => $singularSnake,
            'pluralStudly' => $pluralStudly,
            'pluralKebab' => $pluralKebab,
            'pluralCamel' => $pluralCamel,
            'pluralSnake' => $pluralSnake,
            'tableName' => $pluralSnake,
            'title' => $title,
            'pluralTitle' => $pluralTitle,
            'routePrefix' => $pluralKebab,
            'permissionKey' => $pluralKebab,
            'viewFolder' => $singularKebab,
            'jsFolder' => $singularKebab,
        ];
    }

    /**
     * Load a stub file from the package stubs directory or published overrides.
     */
    public function getStubContent(string $relativePath): string
    {
        $publishedPath = base_path('stubs/renkit/'.ltrim($relativePath, '/'));
        if (File::exists($publishedPath)) {
            return File::get($publishedPath);
        }

        $packagePath = __DIR__.'/../../stubs/'.ltrim($relativePath, '/');
        if (File::exists($packagePath)) {
            return File::get($packagePath);
        }

        throw new \RuntimeException("RenKit stub not found: {$relativePath}");
    }
}
