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
     * @return array<string, string>
     */
    public function generate(array $vars, string $mode = 'simple', string $viewType = 'datatable', bool $force = false): array
    {
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
