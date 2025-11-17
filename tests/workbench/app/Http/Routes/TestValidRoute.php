<?php

namespace App\Http\Routes;

use GeckoDS\LaravelRoutes\Routes\AbstractRouteController;

class TestValidRoute extends AbstractRouteController
{
    public static $handleCalled = false;

    public function handle()
    {
        self::$handleCalled = true;
    }
}
