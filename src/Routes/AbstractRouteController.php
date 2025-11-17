<?php

namespace GeckoDS\LaravelRoutes\Routes;


abstract class AbstractRouteController
{
    abstract public function handle();

    protected function call(array $routes)
    {
        foreach($routes as $route) {
            if (class_exists($route)) {
                $routeInstance = new $route();
                if (is_a($routeInstance, self::class)) {
                    $routeInstance->handle();
                }
            }
        }
    }
}
