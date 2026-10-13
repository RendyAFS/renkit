<?php

namespace RendyAFS\RenKit\Tests;

use Orchestra\Testbench\TestCase as Orchestra;
use RendyAFS\RenKit\RenKitServiceProvider;

abstract class TestCase extends Orchestra
{
    protected function getPackageProviders($app)
    {
        return [
            RenKitServiceProvider::class,
        ];
    }
}
