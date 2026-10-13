<?php

namespace RendyAFS\RenKit\Generators;

use Illuminate\Support\Facades\File;
use RendyAFS\RenKit\Support\StubCompiler;

class FrontendGenerator
{
    public function __construct(
        protected StubCompiler $compiler
    ) {}

    /**
     * Generate all Blade and JavaScript files for the module.
     *
     * @param  array<string, string>  $vars
     * @param  string  $mode  'simple' or 'resource'
     * @param  string  $viewType  'datatable' or 'cardgrid'
     * @param  bool  $force
     * @param  array<string, string>  $schema
     * @return array<string, string>
     */
    public function generate(array $vars, string $mode = 'simple', string $viewType = 'datatable', bool $force = false, array $schema = []): array
    {
        $vars = array_merge($vars, $schema);

        // Fallbacks if schema not provided
        $vars['formInputs'] ??= <<<HTML
<div class="space-y-4">
    <div>
        <label for="{$vars['kebab']}-name" class="block text-sm font-medium mb-2 text-(--color-dark) dark:text-(--color-light)">
            Name <span class="text-(--color-danger)">*</span>
        </label>
        <input type="text" id="{$vars['kebab']}-name" name="name" required
            class="py-2.5 px-4 block w-full border border-(--color-gray) rounded-lg text-sm
            focus:border-(--color-primary) focus:ring-(--color-primary)
            dark:bg-(--color-dark-slate) dark:border-(--color-slate) dark:text-(--color-light)"
            placeholder="Enter {$vars['title']} name">
    </div>

    <div>
        <label for="{$vars['kebab']}-description" class="block text-sm font-medium mb-2 text-(--color-dark) dark:text-(--color-light)">
            Description
        </label>
        <textarea id="{$vars['kebab']}-description" name="description" rows="3"
            class="py-2.5 px-4 block w-full border border-(--color-gray) rounded-lg text-sm
            focus:border-(--color-primary) focus:ring-(--color-primary)
            dark:bg-(--color-dark-slate) dark:border-(--color-slate) dark:text-(--color-light)"
            placeholder="Enter optional description"></textarea>
    </div>

    <div class="flex items-center gap-x-3 pt-2">
        <label for="{$vars['kebab']}-is-active" class="relative inline-block w-11 h-6 cursor-pointer">
            <input type="checkbox" id="{$vars['kebab']}-is-active" name="is_active" value="1" checked class="peer sr-only">
            <span class="absolute inset-0 bg-(--color-dark-gray) rounded-full transition-colors duration-200 ease-in-out peer-checked:bg-(--color-success)"></span>
            <span class="absolute top-1/2 inset-s-0.5 -translate-y-1/2 size-5 bg-(--color-light) rounded-full shadow-sm transition-transform duration-200 ease-in-out peer-checked:translate-x-full"></span>
        </label>
        <span class="text-sm font-medium text-(--color-dark) dark:text-(--color-light)">Active Status</span>
    </div>
</div>
HTML;

        $vars['tableHeaders'] ??= <<<HTML
                    <th class="bg-(--color-light-gray) dark:bg-(--color-dark-slate) px-6 py-3 text-xs font-medium text-muted-foreground-1 uppercase">
                        Name
                    </th>
                    <th class="bg-(--color-light-gray) dark:bg-(--color-dark-slate) px-6 py-3 text-xs font-medium text-muted-foreground-1 uppercase">
                        Description
                    </th>
                    <th class="bg-(--color-light-gray) dark:bg-(--color-dark-slate) px-6 py-3 text-xs font-medium text-muted-foreground-1 uppercase text-center">
                        Status
                    </th>
                    <th class="bg-(--color-light-gray) dark:bg-(--color-dark-slate) px-6 py-3 text-xs font-medium text-muted-foreground-1 uppercase text-center">
                        <i data-lucide="settings" class="size-4 mx-auto"></i>
                    </th>
HTML;

        $vars['jsColumns'] ??= <<<JS
                {
                    data: "name",
                    width: "35%",
                },
                {
                    data: "description",
                    width: "35%",
                    render(data) {
                        return `<div class="whitespace-pre-line text-sm text-muted-foreground-1">\${data ?? "-"}</div>`;
                    },
                },
                {
                    data: "is_active",
                    width: "15%",
                    orderable: false,
                    className: "px-4 py-3 text-center",
                    render(data) {
                        return data
                            ? `<span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium bg-green-100 text-green-800 dark:bg-green-900/30 dark:text-green-400">Active</span>`
                            : `<span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium bg-red-100 text-red-800 dark:bg-red-900/30 dark:text-red-400">Inactive</span>`;
                    },
                },
                {
                    data: "id",
                    width: "15%",
                    orderable: false,
                    searchable: false,
                    className: "px-4 py-3 text-center",
                    render(id, type, row) {
                        return `
                        <div class="flex items-center justify-center gap-2">
                            <button type="button" class="btn-edit p-1.5 rounded-lg text-blue-600 hover:bg-blue-50 dark:hover:bg-blue-900/20" data-id="\${id}" title="Edit">
                                <i data-lucide="square-pen" class="size-4"></i>
                            </button>
                            <button type="button" class="btn-delete p-1.5 rounded-lg text-red-600 hover:bg-red-50 dark:hover:bg-red-900/20" data-id="\${id}" data-name="\${row.name ?? ''}" title="Delete">
                                <i data-lucide="trash-2" class="size-4"></i>
                            </button>
                        </div>
                        `;
                    },
                },
JS;

        $vars['jsPayload'] ??= <<<JS
{
                name: formData.get("name"),
                description: formData.get("description"),
                is_active: formData.get("is_active") ? 1 : 0,
            }
JS;

        $vars['jsReset'] ??= <<<JS
        const activeCheck = form.querySelector("#{$vars['kebab']}-is-active");
        if (activeCheck) activeCheck.checked = true;
JS;

        $vars['jsFill'] ??= <<<JS
        const nameInput = form.querySelector("#{$vars['kebab']}-name");
        const descInput = form.querySelector("#{$vars['kebab']}-description");
        const activeCheck = form.querySelector("#{$vars['kebab']}-is-active");

        if (nameInput) nameInput.value = data.name ?? "";
        if (descInput) descInput.value = data.description ?? "";
        if (activeCheck) activeCheck.checked = Boolean(data.is_active);
JS;

        $created = [];
        $viewsBase = resource_path('views/'.$vars['viewFolder']);
        $jsBase = resource_path('js/pages/'.$vars['jsFolder']);

        $isCardgrid = $viewType === 'cardgrid';

        if ($mode === 'simple') {
            $indexBladeStub = $isCardgrid ? 'views/simple/index.cardgrid.blade.stub' : 'views/simple/index.blade.stub';
            $indexJsStub = $isCardgrid ? 'js/simple/index.cardgrid.js.stub' : 'js/simple/index.js.stub';

            // Blade Views
            $files = [
                'View (Index)' => [$viewsBase.'/index.blade.php', $indexBladeStub],
                'View (Form)' => [$viewsBase.'/form.blade.php', 'views/simple/form.blade.stub'],
                'View (Modal)' => [$viewsBase.'/modal.blade.php', 'views/simple/modal.blade.stub'],
                'JS (Index)' => [$jsBase.'/index.js', $indexJsStub],
            ];
        } else {
            $indexBladeStub = $isCardgrid ? 'views/resource/index.cardgrid.blade.stub' : 'views/resource/index.blade.stub';
            $indexJsStub = $isCardgrid ? 'js/resource/index.cardgrid.js.stub' : 'js/resource/index.js.stub';

            $files = [
                'View (Index)' => [$viewsBase.'/index.blade.php', $indexBladeStub],
                'View (Form)' => [$viewsBase.'/form.blade.php', 'views/resource/form.blade.stub'],
                'View (Create)' => [$viewsBase.'/create.blade.php', 'views/resource/create.blade.stub'],
                'View (Edit)' => [$viewsBase.'/edit.blade.php', 'views/resource/edit.blade.stub'],
                'JS (Index)' => [$jsBase.'/index.js', $indexJsStub],
                'JS (Form)' => [$jsBase.'/form.js', 'js/resource/form.js.stub'],
            ];
        }

        foreach ($files as $label => [$targetPath, $stubPath]) {
            $content = $this->compiler->compile($this->compiler->getStubContent($stubPath), $vars);
            $this->saveFile($targetPath, $content, $force);
            $created[$label] = $targetPath;
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
}
