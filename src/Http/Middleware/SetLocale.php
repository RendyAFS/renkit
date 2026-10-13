<?php

namespace RendyAFS\RenKit\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

class SetLocale
{
    /**
     * Handle an incoming request and set application locale.
     */
    public function handle(Request $request, Closure $next): Response
    {
        $locale = session('locale', config('app.locale', 'id'));

        if (in_array($locale, ['id', 'en'], true)) {
            app()->setLocale($locale);
        }

        return $next($request);
    }
}
