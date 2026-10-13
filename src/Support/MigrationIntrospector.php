<?php

namespace RendyAFS\RenKit\Support;

use Illuminate\Support\Facades\File;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Str;

class MigrationIntrospector
{
    /**
     * Columns that should be excluded from CRUD forms and tables.
     *
     * @var array<string>
     */
    protected array $ignoredColumns = [
        'id',
        'created_at',
        'updated_at',
        'deleted_at',
        'remember_token',
        'email_verified_at',
        'created_by',
        'updated_by',
        'deleted_by',
    ];

    /**
     * Inspect columns for a given table name from migration file or database schema.
     *
     * @param  string  $tableName
     * @return array<array{name: string, type: string, nullable: bool, unique: bool, default: mixed, label: string}>
     */
    public function inspect(string $tableName): array
    {
        // 1. Try Live Database Schema if available
        try {
            if (class_exists(Schema::class) && Schema::hasTable($tableName)) {
                $columns = $this->inspectFromSchema($tableName);
                if (! empty($columns)) {
                    return $columns;
                }
            }
        } catch (\Throwable $e) {
            // Fallback to migration file inspection
        }

        // 2. Parse Migration File
        return $this->inspectFromMigrationFile($tableName);
    }

    /**
     * Inspect columns from the migration file matching the table.
     *
     * @param  string  $tableName
     * @return array<array{name: string, type: string, nullable: bool, unique: bool, default: mixed, label: string}>
     */
    public function inspectFromMigrationFile(string $tableName): array
    {
        $migrationFile = $this->findMigrationFile($tableName);
        if (! $migrationFile || ! File::exists($migrationFile)) {
            return [];
        }

        $content = File::get($migrationFile);
        $columns = [];

        // Match patterns like: $table->string('name')->nullable();
        $pattern = '/\$table->([a-zA-Z0-9_]+)\(\s*[\'"]([a-zA-Z0-9_]+)[\'"](?:\s*,\s*([^)]*))?\)([^;]*);/';
        if (preg_match_all($pattern, $content, $matches, PREG_SET_ORDER)) {
            foreach ($matches as $match) {
                $type = $match[1];
                $name = $match[2];
                $extraChain = $match[4] ?? '';

                if (in_array($name, $this->ignoredColumns, true)) {
                    continue;
                }

                $nullable = str_contains($extraChain, 'nullable()');
                $unique = str_contains($extraChain, 'unique()');

                $default = null;
                if (preg_match('/default\(([^)]+)\)/', $extraChain, $defaultMatch)) {
                    $default = trim($defaultMatch[1], "'\" ");
                }

                $columns[] = [
                    'name' => $name,
                    'type' => $type,
                    'nullable' => $nullable,
                    'unique' => $unique,
                    'default' => $default,
                    'label' => Str::title(str_replace('_', ' ', $name)),
                ];
            }
        }

        return $columns;
    }

    /**
     * Inspect columns from live database schema.
     *
     * @param  string  $tableName
     * @return array<array{name: string, type: string, nullable: bool, unique: bool, default: mixed, label: string}>
     */
    protected function inspectFromSchema(string $tableName): array
    {
        $columns = [];
        $dbColumns = Schema::getColumns($tableName);

        foreach ($dbColumns as $col) {
            $name = $col['name'];
            if (in_array($name, $this->ignoredColumns, true)) {
                continue;
            }

            $type = $col['type_name'] ?? 'string';
            $nullable = (bool) ($col['nullable'] ?? false);
            $default = $col['default'] ?? null;

            $columns[] = [
                'name' => $name,
                'type' => $type,
                'nullable' => $nullable,
                'unique' => false,
                'default' => $default,
                'label' => Str::title(str_replace('_', ' ', $name)),
            ];
        }

        return $columns;
    }

    /**
     * Locate migration file for the target table.
     */
    public function findMigrationFile(string $tableName): ?string
    {
        $patterns = [
            database_path("migrations/*_create_{$tableName}_table.php"),
            database_path("migrations/*_{$tableName}_table.php"),
            database_path("migrations/*_{$tableName}.php"),
        ];

        foreach ($patterns as $pattern) {
            $files = File::glob($pattern);
            if (! empty($files)) {
                return $files[0];
            }
        }

        return null;
    }

    /**
     * Generate HTML inputs for Blade form (Preline UI styled).
     *
     * @param  array<array{name: string, type: string, nullable: bool, unique: bool, default: mixed, label: string}>  $columns
     */
    public function generateFormBlade(array $columns, string $kebab, string $studly): string
    {
        if (empty($columns)) {
            return <<<HTML
<div class="p-6 text-center text-sm text-(--color-dark-gray) border border-dashed border-(--color-gray) rounded-xl">
    <i data-lucide="info" class="size-6 mx-auto mb-2 text-(--color-primary)"></i>
    <p class="font-medium">Belum ada kolom custom pada migration terdeteksi.</p>
    <p class="text-xs text-muted-foreground mt-1">Tambahkan kolom di file migration database lalu jalankan ulang: <code class="px-1.5 py-0.5 rounded bg-(--color-light-gray) font-mono">php artisan renkit:module {$studly}</code></p>
</div>
HTML;
        }

        $html = "<div class=\"space-y-4\">\n";

        foreach ($columns as $col) {
            $name = $col['name'];
            $label = $col['label'];
            $required = ! $col['nullable'];
            $reqMark = $required ? ' <span class="text-(--color-danger)">*</span>' : '';
            $reqAttr = $required ? ' required' : '';

            if ($col['type'] === 'boolean' || $name === 'is_active') {
                $html .= <<<HTML
    <div class="flex items-center gap-x-3 pt-2">
        <label for="{$kebab}-{$name}" class="relative inline-block w-11 h-6 cursor-pointer">
            <input type="checkbox" id="{$kebab}-{$name}" name="{$name}" value="1" checked class="peer sr-only">
            <span class="absolute inset-0 bg-(--color-dark-gray) rounded-full transition-colors duration-200 ease-in-out peer-checked:bg-(--color-success)"></span>
            <span class="absolute top-1/2 inset-s-0.5 -translate-y-1/2 size-5 bg-(--color-light) rounded-full shadow-sm transition-transform duration-200 ease-in-out peer-checked:translate-x-full"></span>
        </label>
        <span class="text-sm font-medium text-(--color-dark) dark:text-(--color-light)">{$label}</span>
    </div>\n
HTML;
            } elseif ($col['type'] === 'text' || $col['type'] === 'longText') {
                $html .= <<<HTML
    <div>
        <label for="{$kebab}-{$name}" class="block text-sm font-medium mb-2 text-(--color-dark) dark:text-(--color-light)">
            {$label}{$reqMark}
        </label>
        <textarea id="{$kebab}-{$name}" name="{$name}" rows="3"{$reqAttr}
            class="py-2.5 px-4 block w-full border border-(--color-gray) rounded-lg text-sm
            focus:border-(--color-primary) focus:ring-(--color-primary)
            dark:bg-(--color-dark-slate) dark:border-(--color-slate) dark:text-(--color-light)"
            placeholder="Masukkan {$label}"></textarea>
    </div>\n
HTML;
            } elseif ($name === 'password') {
                $html .= <<<HTML
    <div x-data="{ show: false }">
        <label for="{$kebab}-{$name}" class="block text-sm font-medium mb-2 text-(--color-dark) dark:text-(--color-light)">
            {$label}{$reqMark}
        </label>
        <div class="relative">
            <input :type="show ? 'text' : 'password'" id="{$kebab}-{$name}" name="{$name}"{$reqAttr}
                class="py-2.5 px-4 pe-10 block w-full border border-(--color-gray) rounded-lg text-sm
                focus:border-(--color-primary) focus:ring-(--color-primary)
                dark:bg-(--color-dark-slate) dark:border-(--color-slate) dark:text-(--color-light)"
                placeholder="••••••••">
            <button type="button" @click="show = !show" tabindex="-1"
                class="absolute inset-y-0 inset-e-3 flex items-center text-(--color-primary)/80 cursor-pointer">
                <i x-show="!show" data-lucide="eye" class="size-4"></i>
                <i x-show="show" data-lucide="eye-off" class="size-4"></i>
            </button>
        </div>
    </div>\n
HTML;
            } elseif (in_array($col['type'], ['integer', 'bigInteger', 'tinyInteger', 'decimal', 'double', 'float'], true)) {
                $html .= <<<HTML
    <div>
        <label for="{$kebab}-{$name}" class="block text-sm font-medium mb-2 text-(--color-dark) dark:text-(--color-light)">
            {$label}{$reqMark}
        </label>
        <input type="number" id="{$kebab}-{$name}" name="{$name}"{$reqAttr}
            class="py-2.5 px-4 block w-full border border-(--color-gray) rounded-lg text-sm
            focus:border-(--color-primary) focus:ring-(--color-primary)
            dark:bg-(--color-dark-slate) dark:border-(--color-slate) dark:text-(--color-light)"
            placeholder="0">
    </div>\n
HTML;
            } elseif (in_array($col['type'], ['date', 'dateTime', 'timestamp'], true)) {
                $html .= <<<HTML
    <div>
        <label for="{$kebab}-{$name}" class="block text-sm font-medium mb-2 text-(--color-dark) dark:text-(--color-light)">
            {$label}{$reqMark}
        </label>
        <input type="date" id="{$kebab}-{$name}" name="{$name}"{$reqAttr}
            class="py-2.5 px-4 block w-full border border-(--color-gray) rounded-lg text-sm
            focus:border-(--color-primary) focus:ring-(--color-primary)
            dark:bg-(--color-dark-slate) dark:border-(--color-slate) dark:text-(--color-light)">
    </div>\n
HTML;
            } elseif ($col['type'] === 'foreignId' || str_ends_with($name, '_id')) {
                $html .= <<<HTML
    <div>
        <x-select id="{$kebab}-{$name}" name="{$name}" label="{$label}{$reqMark}" :options="[]"
            placeholder="Pilih {$label}" search-placeholder="Cari..." clearable="true" />
    </div>\n
HTML;
            } else {
                $inputType = $name === 'email' ? 'email' : 'text';
                $html .= <<<HTML
    <div>
        <label for="{$kebab}-{$name}" class="block text-sm font-medium mb-2 text-(--color-dark) dark:text-(--color-light)">
            {$label}{$reqMark}
        </label>
        <input type="{$inputType}" id="{$kebab}-{$name}" name="{$name}"{$reqAttr}
            class="py-2.5 px-4 block w-full border border-(--color-gray) rounded-lg text-sm
            focus:border-(--color-primary) focus:ring-(--color-primary)
            dark:bg-(--color-dark-slate) dark:border-(--color-slate) dark:text-(--color-light)"
            placeholder="Masukkan {$label}">
    </div>\n
HTML;
            }
        }

        $html .= "</div>";

        return $html;
    }

    /**
     * Generate <th> headers for DataTable.
     *
     * @param  array<array{name: string, type: string, nullable: bool, unique: bool, default: mixed, label: string}>  $columns
     */
    public function generateTableHeaders(array $columns): string
    {
        if (empty($columns)) {
            return <<<HTML
                    <th class="bg-(--color-light-gray) dark:bg-(--color-dark-slate) px-6 py-3 text-xs font-medium text-muted-foreground-1 uppercase">
                        ID
                    </th>
                    <th class="bg-(--color-light-gray) dark:bg-(--color-dark-slate) px-6 py-3 text-xs font-medium text-muted-foreground-1 uppercase text-center">
                        <i data-lucide="settings" class="size-4 mx-auto"></i>
                    </th>
HTML;
        }

        $html = '';
        $displayCols = array_slice($columns, 0, 4);

        foreach ($displayCols as $col) {
            $alignClass = $col['type'] === 'boolean' || $col['name'] === 'is_active' ? ' text-center' : '';
            $html .= <<<HTML
                    <th class="bg-(--color-light-gray) dark:bg-(--color-dark-slate) px-6 py-3 text-xs font-medium text-muted-foreground-1 uppercase{$alignClass}">
                        {$col['label']}
                    </th>\n
HTML;
        }

        $html .= <<<HTML
                    <th class="bg-(--color-light-gray) dark:bg-(--color-dark-slate) px-6 py-3 text-xs font-medium text-muted-foreground-1 uppercase text-center">
                        <i data-lucide="settings" class="size-4 mx-auto"></i>
                    </th>
HTML;

        return $html;
    }

    /**
     * Generate JS datatable column definitions.
     *
     * @param  array<array{name: string, type: string, nullable: bool, unique: bool, default: mixed, label: string}>  $columns
     */
    public function generateJsColumns(array $columns, string $kebab): string
    {
        if (empty($columns)) {
            return <<<JS
                {
                    data: "id",
                    width: "80%",
                },
                {
                    data: "id",
                    width: "20%",
                    orderable: false,
                    searchable: false,
                    className: "px-4 py-3 text-center",
                    render(id, type, row) {
                        return `
                        <div class="flex items-center justify-center gap-2">
                            <button type="button" class="btn-edit p-1.5 rounded-lg text-blue-600 hover:bg-blue-50 dark:hover:bg-blue-900/20" data-id="\${id}" title="Ubah">
                                <i data-lucide="square-pen" class="size-4"></i>
                            </button>
                            <button type="button" class="btn-delete p-1.5 rounded-lg text-red-600 hover:bg-red-50 dark:hover:bg-red-900/20" data-id="\${id}" data-name="ID \${id}" title="Hapus">
                                <i data-lucide="trash-2" class="size-4"></i>
                            </button>
                        </div>
                        `;
                    },
                },
JS;
        }

        $displayCols = array_slice($columns, 0, 4);
        $total = count($displayCols);
        $colWidth = floor(85 / max($total, 1));

        $js = '';
        foreach ($displayCols as $col) {
            $name = $col['name'];
            if ($col['type'] === 'boolean' || $name === 'is_active') {
                $js .= <<<JS
                {
                    data: "{$name}",
                    width: "15%",
                    orderable: false,
                    className: "px-4 py-3 text-center",
                    render(data) {
                        return data
                            ? `<span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium bg-green-100 text-green-800 dark:bg-green-900/30 dark:text-green-400">Aktif</span>`
                            : `<span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium bg-red-100 text-red-800 dark:bg-red-900/30 dark:text-red-400">Nonaktif</span>`;
                    },
                },\n
JS;
            } elseif ($col['type'] === 'text' || $col['type'] === 'longText') {
                $js .= <<<JS
                {
                    data: "{$name}",
                    width: "{$colWidth}%",
                    render(data) {
                        return `<div class="whitespace-pre-line text-sm text-muted-foreground-1">\${data ?? "-"}</div>`;
                    },
                },\n
JS;
            } else {
                $js .= <<<JS
                {
                    data: "{$name}",
                    width: "{$colWidth}%",
                },\n
JS;
            }
        }

        $firstCol = $displayCols[0]['name'] ?? 'id';
        $js .= <<<JS
                {
                    data: "id",
                    width: "15%",
                    orderable: false,
                    searchable: false,
                    className: "px-4 py-3 text-center",
                    render(id, type, row) {
                        return `
                        <div class="flex items-center justify-center gap-2">
                            <button type="button" class="btn-edit p-1.5 rounded-lg text-blue-600 hover:bg-blue-50 dark:hover:bg-blue-900/20" data-id="\${id}" title="Ubah">
                                <i data-lucide="square-pen" class="size-4"></i>
                            </button>
                            <button type="button" class="btn-delete p-1.5 rounded-lg text-red-600 hover:bg-red-50 dark:hover:bg-red-900/20" data-id="\${id}" data-name="\${row.{$firstCol} ?? ''}" title="Hapus">
                                <i data-lucide="trash-2" class="size-4"></i>
                            </button>
                        </div>
                        `;
                    },
                },
JS;

        return $js;
    }

    /**
     * Generate payload extraction object for JS form submit.
     *
     * @param  array<array{name: string, type: string, nullable: bool, unique: bool, default: mixed, label: string}>  $columns
     */
    public function generateJsPayload(array $columns): string
    {
        if (empty($columns)) {
            return "{}";
        }

        $lines = ["{\n"];
        foreach ($columns as $col) {
            $name = $col['name'];
            if ($col['type'] === 'boolean' || $name === 'is_active') {
                $lines[] = "                {$name}: formData.get(\"{$name}\") ? 1 : 0,\n";
            } else {
                $lines[] = "                {$name}: formData.get(\"{$name}\"),\n";
            }
        }
        $lines[] = "            }";

        return implode('', $lines);
    }

    /**
     * Generate resetForm assignments for JS.
     */
    public function generateJsReset(array $columns, string $kebab): string
    {
        $lines = [];
        foreach ($columns as $col) {
            $name = $col['name'];
            if ($col['type'] === 'boolean' || $name === 'is_active') {
                $lines[] = "        const {$name}El = form.querySelector(\"#{$kebab}-{$name}\");";
                $lines[] = "        if ({$name}El) {$name}El.checked = true;";
            }
        }

        return ! empty($lines) ? implode("\n", $lines) : '// Form reset';
    }

    /**
     * Generate fillForm assignments for JS.
     */
    public function generateJsFill(array $columns, string $kebab): string
    {
        $lines = [];
        foreach ($columns as $col) {
            $name = $col['name'];
            if ($col['type'] === 'boolean' || $name === 'is_active') {
                $lines[] = "        const {$name}El = form.querySelector(\"#{$kebab}-{$name}\");";
                $lines[] = "        if ({$name}El) {$name}El.checked = Boolean(data.{$name});";
            } else {
                $lines[] = "        const {$name}El = form.querySelector(\"#{$kebab}-{$name}\");";
                $lines[] = "        if ({$name}El) {$name}El.value = data.{$name} ?? \"\";";
            }
        }

        return ! empty($lines) ? implode("\n", $lines) : '// Form fill';
    }

    /**
     * Generate $fillable array code for Model.
     *
     * @param  array<array{name: string, type: string, nullable: bool, unique: bool, default: mixed, label: string}>  $columns
     */
    public function generateModelFillable(array $columns): string
    {
        if (empty($columns)) {
            return "[\n    ]";
        }

        $names = array_map(fn($col) => "        '{$col['name']}',", $columns);

        return "[\n".implode("\n", $names)."\n    ]";
    }

    /**
     * Generate $casts array code for Model.
     *
     * @param  array<array{name: string, type: string, nullable: bool, unique: bool, default: mixed, label: string}>  $columns
     */
    public function generateModelCasts(array $columns): string
    {
        $casts = [];
        foreach ($columns as $col) {
            $name = $col['name'];
            if ($col['type'] === 'boolean' || $name === 'is_active') {
                $casts[] = "            '{$name}' => 'boolean',";
            } elseif ($name === 'password') {
                $casts[] = "            'password' => 'hashed',";
            } elseif (in_array($col['type'], ['date', 'dateTime', 'timestamp'], true)) {
                $casts[] = "            '{$name}' => 'datetime',";
            } elseif (in_array($col['type'], ['decimal', 'double', 'float'], true)) {
                $casts[] = "            '{$name}' => 'decimal:2',";
            }
        }

        if (empty($casts)) {
            return "[\n        ]";
        }

        return "[\n".implode("\n", $casts)."\n        ]";
    }

    /**
     * Generate validation rules code for FormRequest.
     *
     * @param  array<array{name: string, type: string, nullable: bool, unique: bool, default: mixed, label: string}>  $columns
     */
    public function generateValidationRules(array $columns, string $tableName): string
    {
        if (empty($columns)) {
            return "[\n        ]";
        }

        $rules = [];
        foreach ($columns as $col) {
            $name = $col['name'];
            $ruleParts = [];

            if ($col['nullable']) {
                $ruleParts[] = "'nullable'";
            } else {
                $ruleParts[] = "'required'";
            }

            if ($col['type'] === 'boolean' || $name === 'is_active') {
                $ruleParts[] = "'boolean'";
            } elseif ($name === 'email') {
                $ruleParts[] = "'email'";
                $ruleParts[] = "'max:255'";
            } elseif ($col['type'] === 'text' || $col['type'] === 'longText') {
                $ruleParts[] = "'string'";
            } elseif (in_array($col['type'], ['integer', 'bigInteger', 'tinyInteger'], true)) {
                $ruleParts[] = "'integer'";
            } elseif (in_array($col['type'], ['decimal', 'double', 'float'], true)) {
                $ruleParts[] = "'numeric'";
            } elseif (in_array($col['type'], ['date', 'dateTime', 'timestamp'], true)) {
                $ruleParts[] = "'date'";
            } else {
                $ruleParts[] = "'string'";
                $ruleParts[] = "'max:255'";
            }

            $rules[] = "            '{$name}' => [".implode(', ', $ruleParts).'],';
        }

        return "[\n".implode("\n", $rules)."\n        ]";
    }
}
