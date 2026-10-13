<?php

namespace RendyAFS\RenKit\Support;

use Illuminate\Support\Facades\File;

class AuthScaffoldSetup
{
    /**
     * Run the complete authentication scaffolding setup.
     *
     * @return array{status: bool, message: string}
     */
    public function setup(): array
    {
        $this->publishAuthViews();
        $this->publishAuthJs();
        $this->publishFortifyProvider();
        $this->publishFortifyConfig();
        $this->registerProviderInBootstrap();
        $this->ensureAuthRoutesFallback();

        return [
            'status' => true,
            'message' => 'Authentication scaffolding (Views, Fortify Provider, Config & Routes) setup successfully.',
        ];
    }

    /**
     * Publish auth blade views and layouts.
     */
    public function publishAuthViews(): void
    {
        // Auth views (login, register)
        $sourceAuth = __DIR__.'/../../resources/views/auth';
        $destAuth = resource_path('views/auth');
        if (File::isDirectory($sourceAuth)) {
            File::ensureDirectoryExists($destAuth);
            File::copyDirectory($sourceAuth, $destAuth);
        }

        // Layout auth
        $sourceLayoutAuth = __DIR__.'/../../resources/views/layouts/auth.blade.php';
        $destLayoutAuth = resource_path('views/layouts/auth.blade.php');
        if (File::exists($sourceLayoutAuth)) {
            File::ensureDirectoryExists(dirname($destLayoutAuth));
            File::put($destLayoutAuth, File::get($sourceLayoutAuth));
        }

        // Slideshow component
        $sourceSlide = __DIR__.'/../../resources/views/components/auth-slideshow.blade.php';
        $destSlide = resource_path('views/components/auth-slideshow.blade.php');
        if (File::exists($sourceSlide)) {
            File::ensureDirectoryExists(dirname($destSlide));
            File::put($destSlide, File::get($sourceSlide));
        }
    }

    /**
     * Publish auth JavaScript helpers.
     */
    public function publishAuthJs(): void
    {
        $sourceJs = __DIR__.'/../../resources/js/pages/auth';
        $destJs = resource_path('js/pages/auth');
        if (File::isDirectory($sourceJs)) {
            File::ensureDirectoryExists($destJs);
            File::copyDirectory($sourceJs, $destJs);
        }
    }

    /**
     * Publish App\Providers\FortifyServiceProvider.php
     */
    public function publishFortifyProvider(): void
    {
        $target = app_path('Providers/FortifyServiceProvider.php');
        File::ensureDirectoryExists(dirname($target));

        $content = <<<'PHP'
<?php

namespace App\Providers;

use App\Models\User;
use Illuminate\Cache\RateLimiting\Limit;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\RateLimiter;
use Illuminate\Support\ServiceProvider;
use Illuminate\Validation\ValidationException;
use Laravel\Fortify\Fortify;

class FortifyServiceProvider extends ServiceProvider
{
    /**
     * Register any application services.
     */
    public function register(): void
    {
        //
    }

    /**
     * Bootstrap any application services.
     */
    public function boot(): void
    {
        RateLimiter::for('login', function (Request $request) {
            $throttleKey = $request->input('email').'|'.$request->ip();

            return Limit::perMinute(5)->by($throttleKey);
        });

        if (class_exists(Fortify::class)) {
            Fortify::authenticateUsing(function (Request $request) {
                $request->validate([
                    'email' => ['required', 'string'],
                    'password' => ['required', 'string'],
                ]);

                $user = User::where('email', $request->email)
                    ->orWhere('name', $request->email)
                    ->first();

                if (! $user || ! Hash::check($request->password, $user->password)) {
                    throw ValidationException::withMessages([
                        'email' => trans('auth.failed'),
                    ]);
                }

                if (isset($user->is_active) && ! $user->is_active) {
                    throw ValidationException::withMessages([
                        'email' => 'Akun Anda sedang dinonaktifkan.',
                    ]);
                }

                return $user;
            });

            Fortify::loginView(function () {
                return view('auth.login');
            });

            Fortify::registerView(function () {
                return view('auth.register');
            });
        }
    }
}
PHP;

        File::put($target, $content."\n");
    }

    /**
     * Publish config/fortify.php if not exists.
     */
    public function publishFortifyConfig(): void
    {
        $target = config_path('fortify.php');
        if (! File::exists($target)) {
            $content = <<<'PHP'
<?php

use Laravel\Fortify\Features;

return [

    'guard' => 'web',

    'middleware' => ['web'],

    'auth_middleware' => 'auth',

    'passwords' => 'users',

    'username' => 'email',

    'email' => 'email',

    'views' => true,

    'home' => '/dashboard',

    'prefix' => '',

    'domain' => null,

    'lowercase_usernames' => true,

    'limiters' => [
        'login' => 'login',
        'two-factor' => 'two-factor',
    ],

    'features' => [
        Features::registration(),
        Features::resetPasswords(),
        // Features::emailVerification(),
        Features::updateProfileInformation(),
        Features::updatePasswords(),
        // Features::twoFactorAuthentication(),
    ],

];
PHP;
            File::ensureDirectoryExists(dirname($target));
            File::put($target, $content."\n");
        }
    }

    /**
     * Register FortifyServiceProvider in bootstrap/providers.php (Laravel 11/12)
     */
    public function registerProviderInBootstrap(): void
    {
        $providersPath = base_path('bootstrap/providers.php');
        if (File::exists($providersPath)) {
            $content = File::get($providersPath);
            if (! str_contains($content, 'App\Providers\FortifyServiceProvider::class')) {
                $updated = preg_replace(
                    '/(\];\s*)$/',
                    "    App\\Providers\\FortifyServiceProvider::class,\n];",
                    $content
                );
                if ($updated) {
                    File::put($providersPath, $updated."\n");
                }
            }
        }
    }

    /**
     * Ensure standalone fallback auth routes if Fortify is not active.
     */
    public function ensureAuthRoutesFallback(): void
    {
        $webRoutesPath = base_path('routes/web.php');
        if (! File::exists($webRoutesPath)) {
            return;
        }

        // If fortify is installed, fortify automatically handles /login, /register, /logout
        if (class_exists(\Laravel\Fortify\Fortify::class)) {
            return;
        }

        $authRoutePath = base_path('routes/web/auth.php');
        File::ensureDirectoryExists(dirname($authRoutePath));

        if (! File::exists($authRoutePath)) {
            $content = <<<'PHP'
<?php

use Illuminate\Support\Facades\Route;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

Route::middleware('guest')->group(function () {
    Route::get('/login', fn() => view('auth.login'))->name('login');
    Route::post('/login', function (Request $request) {
        $credentials = $request->validate([
            'email' => ['required'],
            'password' => ['required'],
        ]);

        if (Auth::attempt($credentials, (bool) $request->boolean('remember'))) {
            $request->session()->regenerate();
            return redirect()->intended('/dashboard');
        }

        return back()->withErrors([
            'email' => 'Kombinasi kredensial tidak cocok.',
        ])->onlyInput('email');
    });

    Route::get('/register', fn() => view('auth.register'))->name('register');
    Route::post('/register', function (Request $request) {
        $validated = $request->validate([
            'name' => ['required', 'string', 'max:255'],
            'email' => ['required', 'string', 'email', 'max:255', 'unique:users'],
            'password' => ['required', 'string', 'min:8', 'confirmed'],
        ]);

        $user = \App\Models\User::create([
            'name' => $validated['name'],
            'email' => $validated['email'],
            'password' => \Illuminate\Support\Facades\Hash::make($validated['password']),
        ]);

        Auth::login($user);
        return redirect('/dashboard');
    });
});

Route::post('/logout', function (Request $request) {
    Auth::logout();
    $request->session()->invalidate();
    $request->session()->regenerateToken();
    return redirect('/login');
})->middleware('auth')->name('logout');
PHP;
            File::put($authRoutePath, $content."\n");

            $webContent = File::get($webRoutesPath);
            if (! str_contains($webContent, "require __DIR__.'/web/auth.php';")) {
                File::append($webRoutesPath, "\nrequire __DIR__.'/web/auth.php';\n");
            }
        }
    }
}
