<?php

namespace RendyAFS\RenKit\Support;

use Illuminate\Support\Facades\File;

class FrontendToolchainSetup
{
    /**
     * Run the complete frontend toolchain setup.
     *
     * @return array{status: bool, message: string}
     */
    public function setup(): array
    {
        $this->updatePackageJson();
        $this->updateViteConfig();
        $this->publishCss();
        $this->publishJs();
        $this->publishVendorJs();

        return [
            'status' => true,
            'message' => 'Frontend Toolchain (Preline, Tailwind v4, Vite & JS utils) configured successfully.',
        ];
    }

    /**
     * Update package.json with Preline and Tailwind v4 dependencies.
     */
    public function updatePackageJson(): void
    {
        $path = base_path('package.json');
        if (! File::exists($path)) {
            return;
        }

        $content = json_decode(File::get($path), true) ?: [];

        $devDependencies = [
            '@tailwindcss/forms' => '^0.5.11',
            '@tailwindcss/vite' => '^4.0.0',
            'laravel-vite-plugin' => $content['devDependencies']['laravel-vite-plugin'] ?? '^1.2.0',
            'tailwindcss' => '^4.0.0',
            'vite' => $content['devDependencies']['vite'] ?? '^6.0.11',
        ];

        $dependencies = [
            '@floating-ui/dom' => '^1.7.6',
            '@preline/datatable' => '^3.2.3',
            'alpinejs' => '^3.15.3',
            'datatables.net-dt' => '^2.3.6',
            'flatpickr' => '^4.6.13',
            'lucide' => '^0.562.0',
            'preline' => '^3.2.3',
        ];

        if (! isset($content['devDependencies'])) {
            $content['devDependencies'] = [];
        }

        if (! isset($content['dependencies'])) {
            $content['dependencies'] = [];
        }

        foreach ($devDependencies as $pkg => $ver) {
            $content['devDependencies'][$pkg] = $ver;
        }

        foreach ($dependencies as $pkg => $ver) {
            $content['dependencies'][$pkg] = $ver;
        }

        File::put($path, json_encode($content, JSON_PRETTY_PRINT | JSON_UNESCAPED_SLASHES)."\n");
    }

    /**
     * Update vite.config.js to include Tailwind v4 and native modular page script discovery without extra packages.
     */
    public function updateViteConfig(): void
    {
        $path = base_path('vite.config.js');

        $viteContent = <<<'JS'
import { defineConfig } from "vite";
import laravel from "laravel-vite-plugin";
import tailwindcss from "@tailwindcss/vite";
import fs from "node:fs";
import path from "node:path";

function getFiles(dir, matchExt = ".js") {
    if (!fs.existsSync(dir)) return [];
    const files = [];
    const items = fs.readdirSync(dir, { withFileTypes: true });
    for (const item of items) {
        const fullPath = path.join(dir, item.name).replace(/\\/g, "/");
        if (item.isDirectory()) {
            files.push(...getFiles(fullPath, matchExt));
        } else if (item.name.endsWith(matchExt)) {
            files.push(fullPath);
        }
    }
    return files;
}

export default defineConfig({
    plugins: [
        laravel({
            input: [
                "resources/css/app.css",
                "resources/js/app.js",
                ...getFiles("resources/js/pages"),
            ],
            refresh: true,
        }),
        tailwindcss(),
    ],
    server: {
        host: "127.0.0.1",
        port: 5173,
        headers: {
            "Access-Control-Allow-Origin": "*",
        },
    },
});
JS;

        File::put($path, $viteContent."\n");
    }

    /**
     * Publish CSS styling assets to resources/css/.
     */
    public function publishCss(): void
    {
        $source = __DIR__.'/../../resources/css';
        $dest = resource_path('css');

        if (File::isDirectory($source)) {
            File::ensureDirectoryExists($dest);
            File::copyDirectory($source, $dest);
        }
    }

    /**
     * Publish JS app entry and utilities to resources/js/.
     */
    public function publishJs(): void
    {
        $sourceUtils = __DIR__.'/../../resources/js/utils';
        $destUtils = resource_path('js/utils');

        if (File::isDirectory($sourceUtils)) {
            File::ensureDirectoryExists($destUtils);
            File::copyDirectory($sourceUtils, $destUtils);
        }

        $sourceAppJs = __DIR__.'/../../resources/js/app.js';
        $destAppJs = resource_path('js/app.js');

        if (File::exists($sourceAppJs)) {
            File::ensureDirectoryExists(dirname($destAppJs));
            File::put($destAppJs, File::get($sourceAppJs));
        }
    }

    /**
     * Publish offline vendor JS scripts (jquery, blockUi) to public/js/.
     */
    public function publishVendorJs(): void
    {
        $source = __DIR__.'/../../public/js';
        $dest = public_path('js');

        if (File::isDirectory($source)) {
            File::ensureDirectoryExists($dest);
            File::copyDirectory($source, $dest);
        }
    }
}
