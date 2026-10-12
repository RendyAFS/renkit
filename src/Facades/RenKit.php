<?php

namespace RendyAFS\RenKit\Facades;

use Illuminate\Support\Facades\Facade;

/**
 * @method static string version()
 * @method static array getFeatures()
 * @method static bool hasFeature(string $feature)
 *
 * @see \RendyAFS\RenKit\RenKit
 */
class RenKit extends Facade
{
    /**
     * Get the registered name of the component.
     */
    protected static function getFacadeAccessor(): string
    {
        return 'renkit';
    }
}
