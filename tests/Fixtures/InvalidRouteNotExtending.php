<?php

namespace GeckoDS\LaravelRoutes\Tests\Fixtures;

/**
 * This class should not be called because it does not extend AbstractRouteController
 */
class InvalidRouteNotExtending
{
    public function handle()
    {
        // This should not be called
    }
}
