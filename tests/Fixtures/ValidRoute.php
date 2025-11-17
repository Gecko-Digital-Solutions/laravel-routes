<?php

namespace GeckoDS\LaravelRoutes\Tests\Fixtures;

use GeckoDS\LaravelRoutes\Routes\AbstractRouteController;

class ValidRoute extends AbstractRouteController
{
    public static $handleCalled = false;

    public function handle()
    {
        self::$handleCalled = true;
    }
}
