<?php

namespace RendyAFS\RenKit\Commands;

use Illuminate\Console\Command;
use RendyAFS\RenKit\Support\EnvironmentDetector;

class RenKitDoctorCommand extends Command
{
    protected $signature = 'renkit:doctor';

    protected $description = 'Diagnose project environment and dependency readiness for RenKit';

    public function handle(EnvironmentDetector $detector): int
    {
        $this->newLine();
        $this->components->info('🩺 RenKit Doctor — Environment Diagnostic');
        $this->line(str_repeat('─', 60));

        $checks = $detector->inspect();
        $hasErrors = false;

        foreach ($checks as $item) {
            $status = $item['status'] ? '<fg=green>PASS</>' : '<fg=yellow>INFO</>';
            $this->components->twoColumnDetail($item['label'], "{$status}  {$item['message']}");
            if (! $item['status'] && str_contains($item['message'], 'Requirement')) {
                $hasErrors = true;
            }
        }

        $this->newLine();

        if ($hasErrors) {
            $this->components->error('Some core requirements are not satisfied. Please resolve them before using RenKit.');

            return self::FAILURE;
        }

        $this->components->info('🎉 Environment is ready! RenKit commands and generators can be run safely.');
        $this->newLine();

        return self::SUCCESS;
    }
}
