<?php

namespace RendyAFS\RenKit\Generators;

use Illuminate\Support\Facades\File;

class MenuIntegrator
{
    /**
     * Add menu entry to config/menu.php safely.
     *
     * @param  array<string, string>  $vars
     * @return array{status: bool, message: string}
     */
    public function integrate(array $vars, string $icon = 'box'): array
    {
        $menuPath = config_path('menu.php');

        if (! File::exists($menuPath)) {
            return [
                'status' => false,
                'message' => 'config/menu.php file not found.',
            ];
        }

        $content = File::get($menuPath);
        $url = '/'.$vars['routePrefix'];

        if (str_contains($content, "'url'  => '{$url}'") || str_contains($content, "'url' => '{$url}'") || str_contains($content, "\"url\" => \"{$url}\"")) {
            return [
                'status' => true,
                'message' => "Menu for '{$vars['title']}' ({$url}) already exists in config/menu.php.",
            ];
        }

        $menuItemSnippet = <<<PHP

    // {$vars['title']}
    [
        'name' => '{$vars['title']}',
        'icon' => '{$icon}',
        'url'  => '{$url}',
    ],
];
PHP;

        // Replace the final '];' with the new entry + '];'
        $updated = preg_replace('/(\n\];\s*)$/', $menuItemSnippet."\n", $content);

        if ($updated === null || $updated === $content) {
            return [
                'status' => false,
                'message' => 'Unable to append menu entry to config/menu.php automatically.',
            ];
        }

        File::put($menuPath, $updated);

        return [
            'status' => true,
            'message' => "Successfully added '{$vars['title']}' to config/menu.php",
        ];
    }
}
