<?php

namespace GeckoDS\LaravelRoutes\Tests;

use Orchestra\Testbench\TestCase as Orchestra;
use GeckoDS\LaravelRoutes\Providers\LaravelRoutesProvider;

abstract class TestCase extends Orchestra
{
    /**
     * Load the service provider for the package.
     *
     * @param  \Illuminate\Foundation\Application  $app
     * @return array
     */
    protected function getPackageProviders($app)
    {
        return [
            LaravelRoutesProvider::class,
        ];
    }

    /**
     * Define the test environment setup.
     *
     * @param  \Illuminate\Foundation\Application  $app
     */
    protected function getEnvironmentSetUp($app)
    {
        // Use the testbench structure
        $app['config']->set('routes.path', app_path('Http/Routes/**/*.php'));
        $app['config']->set('routes.prefix_namespace', 'App\\Http\\Routes\\');
        $app['config']->set('routes.other_routes', []);
    }

    /**
     * Get package aliases.
     *
     * @param  \Illuminate\Foundation\Application  $app
     *
     * @return array
     */
    protected function getApplicationBasePath()
    {
        return __DIR__ . '/workbench';
    }
}