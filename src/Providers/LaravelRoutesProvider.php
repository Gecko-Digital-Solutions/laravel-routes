<?php

namespace GeckoDS\LaravelRoutes\Providers;

use Illuminate\Support\ServiceProvider;

class LaravelRoutesProvider extends ServiceProvider
{
    /**
     * Boot the service provider.
     *
     * @return void
     */
    public function boot()
    {
        $this->publishes([
            __DIR__.'/../Config/routes.php' => config_path('routes.php'),
        ], 'config');
    }

    public function register()
    {
        $this->mergeConfigFrom(
            __DIR__.'/../Config/routes.php', 'routes'
        );
    }
}

