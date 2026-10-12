<?php

namespace RendyAFS\RenKit\Commands;

use Illuminate\Console\Command;

class RenKitAboutCommand extends Command
{
    protected $signature = 'renkit:about';

    protected $description = 'Display information and status of RenKit package';

    public function handle(): int
    {
        $this->newLine();
        $this->components->info('⚡ RenKit — Laravel Development Toolkit');
        $this->line('  by RendyAFS (https://github.com/RendyAFS/renkit)');
        $this->newLine();

        $this->components->twoColumnDetail('Version', config('renkit.version', '1.0.0'));
        $this->components->twoColumnDetail('UI Framework', config('renkit.ui.framework', 'preline').' (Tailwind CSS v4)');
        $this->components->twoColumnDetail('Component Wrappers', config('renkit.ui.use_component_wrappers', true) ? 'Enabled' : 'Disabled');
        $this->components->twoColumnDetail('Default CRUD Mode', config('renkit.generator.default_mode', 'simple'));
        $this->components->twoColumnDetail('Localization Feature', config('renkit.features.localization', true) ? 'Enabled' : 'Disabled');
        $this->components->twoColumnDetail('Theme Feature', config('renkit.features.theme', true) ? 'Enabled' : 'Disabled');
        $this->components->twoColumnDetail('Spatie Permission Feature', config('renkit.features.permission', true) ? 'Enabled' : 'Disabled');
        $this->components->twoColumnDetail('Menu Auto-Integration', config('renkit.features.menu', true) ? 'Enabled' : 'Disabled');

        $this->newLine();
        $this->line('  Ready to generate modules with: <fg=cyan>php artisan renkit:module {Name} [--simple|--resource]</>');
        $this->newLine();

        return self::SUCCESS;
    }
}
