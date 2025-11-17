<?php

namespace GeckoDS\LaravelRoutes\Routes;

use Illuminate\Support\Collection;
use ReflectionClass;

class RouteController extends AbstractRouteController
{
    public function handle()
    {
        $this->call([...config('routes.other_routes', []), ...$this->getRouteClasses()->toArray()]);
    }

    public function getRouteClasses(): Collection
    {
        return collect(glob(config('routes.path', app_path('Http/**/*.php')), GLOB_BRACE))
            ->map(function($file) {
                //require_once $file;
                return [
                    'filename' => $file,
                    'classname' => config('routes.prefix_namespace', '') . str_replace([app_path('/'), '/', '.php'], ['', '\\', ''], $file)
                ];
            })->filter(function($item) {
                require_once $item['filename'];
                if (class_exists($item['classname'])) {
                    $reflection = new ReflectionClass($item['classname']);
                    return $reflection->isSubclassOf(AbstractRouteController::class);
                }

                return false;
            })->map(function($item) {
                return $item['classname'];
            });
    }
}



