<?php

namespace RendyAFS\RenKit\Support;

use Illuminate\Support\Facades\File;

class LayoutScaffoldSetup
{
    /**
     * Run the complete layout and shell scaffolding.
     *
     * @return array{status: bool, message: string}
     */
    public function setup(): array
    {
        $this->publishLayouts();
        $this->publishComponents();
        $this->publishComponentClasses();
        $this->publishDashboard();
        $this->ensureConfigMenu();

        return [
            'status' => true,
            'message' => 'Base Layout, Sidebar, Navbar & Menu Shell configured successfully.',
        ];
    }

    /**
     * Publish resources/views/layouts/main.blade.php & lang.blade.php.
     */
    public function publishLayouts(): void
    {
        $source = __DIR__.'/../../resources/views/layouts';
        $dest = resource_path('views/layouts');

        if (File::isDirectory($source)) {
            File::ensureDirectoryExists($dest);
            File::copyDirectory($source, $dest);
        }
    }

    /**
     * Publish reusable Blade components (sidebar, navbar, datatable, modal, etc.).
     */
    public function publishComponents(): void
    {
        $source = __DIR__.'/../../resources/views/components';
        $dest = resource_path('views/components');

        if (File::isDirectory($source)) {
            File::ensureDirectoryExists($dest);
            File::copyDirectory($source, $dest);
        }
    }

    /**
     * Publish component PHP classes (App\View\Components\Sidebar & Navbar).
     */
    public function publishComponentClasses(): void
    {
        $targetDir = app_path('View/Components');
        File::ensureDirectoryExists($targetDir);

        $sidebarStub = __DIR__.'/../../stubs/components/Sidebar.php.stub';
        $navbarStub = __DIR__.'/../../stubs/components/Navbar.php.stub';

        $destSidebar = $targetDir.'/Sidebar.php';
        $destNavbar = $targetDir.'/Navbar.php';

        if (File::exists($sidebarStub) && ! File::exists($destSidebar)) {
            File::put($destSidebar, File::get($sidebarStub));
        }

        if (File::exists($navbarStub) && ! File::exists($destNavbar)) {
            File::put($destNavbar, File::get($navbarStub));
        }
    }

    /**
     * Publish starter Dashboard view and route.
     */
    public function publishDashboard(): void
    {
        // 1. Dashboard View
        $destView = resource_path('views/dashboard/index.blade.php');
        $sourceView = __DIR__.'/../../resources/views/dashboard/index.blade.php';

        if (File::exists($sourceView)) {
            File::ensureDirectoryExists(dirname($destView));
            if (! File::exists($destView)) {
                File::put($destView, File::get($sourceView));
            }
        }

        // 2. Dashboard Route
        $dashboardRoutePath = base_path('routes/web/dashboard.php');
        File::ensureDirectoryExists(dirname($dashboardRoutePath));

        if (! File::exists($dashboardRoutePath)) {
            $routeContent = <<<'PHP'
<?php

use Illuminate\Support\Facades\Route;

Route::get('/dashboard', function () {
    return view('dashboard.index');
})->name('dashboard');
PHP;
            File::put($dashboardRoutePath, $routeContent."\n");
        }

        // 3. Include in routes/web.php
        $webRoutesPath = base_path('routes/web.php');
        if (File::exists($webRoutesPath)) {
            $webContent = File::get($webRoutesPath);
            if (! str_contains($webContent, "require __DIR__.'/web/dashboard.php';") && ! str_contains($webContent, "require __DIR__ . '/web/dashboard.php';")) {
                $appendSnippet = "\nrequire __DIR__.'/web/dashboard.php';\n";
                File::append($webRoutesPath, $appendSnippet);
            }
        }
    }

    /**
     * Ensure config/menu.php exists with starter configuration.
     */
    public function ensureConfigMenu(): void
    {
        $dest = config_path('menu.php');
        if (! File::exists($dest)) {
            $source = __DIR__.'/../../config/menu.php';
            File::ensureDirectoryExists(dirname($dest));
            if (File::exists($source)) {
                File::put($dest, File::get($source));
            }
        }
    }
}
