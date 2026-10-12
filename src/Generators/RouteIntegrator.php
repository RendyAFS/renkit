<?php

namespace RendyAFS\RenKit\Generators;

use Illuminate\Support\Facades\File;
use RendyAFS\RenKit\Support\StubCompiler;

class RouteIntegrator
{
    public function __construct(
        protected StubCompiler $compiler
    ) {}

    /**
     * Integrate modular route file and register it in routes/web.php.
     *
     * @param  array<string, string>  $vars
     * @return array{created_file: string, registered_in_web: bool}
     */
    public function integrate(array $vars, bool $force = false): array
    {
        $fileName = $vars['pluralKebab'].'.php';
        $routesWebDir = base_path(config('renkit.paths.routes_dir', 'routes/web'));
        File::ensureDirectoryExists($routesWebDir);

        $moduleRoutePath = $routesWebDir.'/'.$fileName;

        // Generate module route file
        $stubContent = $this->compiler->getStubContent('routes.stub');
        $compiled = $this->compiler->compile($stubContent, $vars);

        if (! File::exists($moduleRoutePath) || $force) {
            File::put($moduleRoutePath, $compiled);
        }

        // Register in main routes/web.php
        $registeredInWeb = $this->registerInWebRoutes($fileName);

        return [
            'created_file' => $moduleRoutePath,
            'registered_in_web' => $registeredInWeb,
        ];
    }

    /**
     * Safely register the modular route file inside routes/web.php.
     */
    protected function registerInWebRoutes(string $fileName): bool
    {
        $webRoutesPath = base_path(config('renkit.paths.routes_main', 'routes/web.php'));

        if (! File::exists($webRoutesPath)) {
            return false;
        }

        $webContent = File::get($webRoutesPath);
        $requireStatement = "    require __DIR__.'/web/{$fileName}';";

        if (str_contains($webContent, "'/web/{$fileName}'") || str_contains($webContent, "\"/web/{$fileName}\"")) {
            return true; // Already registered
        }

        // Search for the auth middleware group
        $pattern = "/(Route::middleware\(\['auth'[^\]]*\]\)->group\(function\s*\(\)\s*\{)/";
        if (preg_match($pattern, $webContent)) {
            // Append just before the end of the group
            // Find closing of that group or append before closing line
            $updated = preg_replace_callback(
                "/(Route::middleware\(\['auth'[^\]]*\]\)->group\(function\s*\(\)\s*\{[\s\S]*?)(\n\}\);)/",
                function ($matches) use ($requireStatement) {
                    return $matches[1]."\n".$requireStatement.$matches[2];
                },
                $webContent,
                1
            );

            if ($updated !== null && $updated !== $webContent) {
                File::put($webRoutesPath, $updated);

                return true;
            }
        }

        // Fallback: append at bottom of web.php
        File::append($webRoutesPath, "\n".$requireStatement."\n");

        return true;
    }

    /**
     * Remove route require statement from routes/web.php.
     */
    public function removeRegistration(string $fileName): bool
    {
        $webRoutesPath = base_path(config('renkit.paths.routes_main', 'routes/web.php'));

        if (! File::exists($webRoutesPath)) {
            return false;
        }

        $webContent = File::get($webRoutesPath);
        $escapedFileName = preg_quote($fileName, '/');
        $updated = preg_replace("/\n\s*require\s+__DIR__\s*\.\s*['\"]\/web\/{$escapedFileName}['\"];/", '', $webContent);

        if ($updated !== null && $updated !== $webContent) {
            File::put($webRoutesPath, $updated);

            return true;
        }

        return false;
    }
}
