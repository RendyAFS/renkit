<?php

namespace RendyAFS\RenKit\Generators;

use Illuminate\Support\Facades\File;

class LangIntegrator
{
    /**
     * Integrate translation strings for the module.
     *
     * @param  array<string, string>  $vars
     * @return array{status: bool, created_files: array<string>}
     */
    public function integrate(array $vars, bool $force = false): array
    {
        $createdFiles = [];

        // 1. Append to lang/id/models.php and lang/en/models.php if they exist
        $this->appendModelTranslation('id', $vars['studly'], $vars['title']);
        $this->appendModelTranslation('en', $vars['studly'], $vars['title']);

        // 2. Generate dedicated lang/id/{kebab}.php and lang/en/{kebab}.php
        $idLangPath = base_path("lang/id/{$vars['kebab']}.php");
        $enLangPath = base_path("lang/en/{$vars['kebab']}.php");

        if (File::isDirectory(base_path('lang/id')) && (! File::exists($idLangPath) || $force)) {
            $idContent = <<<PHP
<?php

return [
    'title' => '{$vars['title']}',
    'description' => 'Kelola data {$vars['title']}',
    'fields' => [
        'name' => 'Nama',
        'description' => 'Deskripsi',
        'is_active' => 'Status Aktif',
    ],
];
PHP;
            File::put($idLangPath, $idContent);
            $createdFiles[] = $idLangPath;
        }

        if (File::isDirectory(base_path('lang/en')) && (! File::exists($enLangPath) || $force)) {
            $enContent = <<<PHP
<?php

return [
    'title' => '{$vars['title']}',
    'description' => 'Manage {$vars['title']} data',
    'fields' => [
        'name' => 'Name',
        'description' => 'Description',
        'is_active' => 'Active Status',
    ],
];
PHP;
            File::put($enLangPath, $enContent);
            $createdFiles[] = $enLangPath;
        }

        return [
            'status' => true,
            'created_files' => $createdFiles,
        ];
    }

    /**
     * Safely append a model key to lang/{locale}/models.php.
     */
    protected function appendModelTranslation(string $locale, string $key, string $value): bool
    {
        $path = base_path("lang/{$locale}/models.php");

        if (! File::exists($path)) {
            return false;
        }

        $content = File::get($path);

        // Check if key already exists
        if (str_contains($content, "'{$key}'") || str_contains($content, "\"{$key}\"")) {
            return true;
        }

        $entry = "    '{$key}' => '{$value}',\n];";
        $updated = preg_replace('/(\n\];\s*)$/', "\n".$entry, $content);

        if ($updated !== null && $updated !== $content) {
            File::put($path, $updated);

            return true;
        }

        return false;
    }

    /**
     * Remove integration during rollback or testing.
     */
    public function removeIntegration(string $studly, string $kebab): void
    {
        foreach (['id', 'en'] as $locale) {
            $modelsPath = base_path("lang/{$locale}/models.php");
            if (File::exists($modelsPath)) {
                $content = File::get($modelsPath);
                $updated = preg_replace("/\n\s*['\"]{$studly}['\"]\s*=>\s*['\"][^'\"]+['\"],?/", '', $content);
                if ($updated !== null && $updated !== $content) {
                    File::put($modelsPath, $updated);
                }
            }

            $moduleLang = base_path("lang/{$locale}/{$kebab}.php");
            if (File::exists($moduleLang)) {
                File::delete($moduleLang);
            }
        }
    }
}
