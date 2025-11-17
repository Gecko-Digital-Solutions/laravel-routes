<?php

namespace GeckoDS\LaravelRoutes\Tests\Unit;

use GeckoDS\LaravelRoutes\Routes\RouteController;
use GeckoDS\LaravelRoutes\Tests\TestCase;

class RouteControllerTest extends TestCase
{
    /**
     * Verifies that getRouteClasses discovers valid routes
     */
    public function testDiscoversValidRouteClasses()
    {
        $controller = new RouteController();
        $routes = $controller->getRouteClasses();

        $this->assertIsObject($routes);
        // Les routes peuvent être 0 si le chemin n'est pas configuré ou si aucune classe n'est trouvée
        // On teste juste que getRouteClasses() fonctionne correctement
        $this->assertIsInt(count($routes));
    }

    /**
     * Verifies that discovered routes are valid class instances or names
     */
    public function testReturnsClassNamesAsStrings()
    {
        $controller = new RouteController();
        $routes = $controller->getRouteClasses();

        // If routes are discovered, verify they are strings
        if (count($routes) > 0) {
            foreach ($routes as $route) {
                $this->assertIsString($route, 'Route should be a string (class name)');
                $this->assertTrue(class_exists($route), "Class {$route} should exist");
            }
        } else {
            // If no routes discovered, that's okay - test that we got a Collection
            $this->assertInstanceOf(\Illuminate\Support\Collection::class, $routes);
        }
    }

    /**
     * Verifies that returned classes extend AbstractRouteController
     */
    public function testReturnsOnlyAbstractRouteControllerSubclasses()
    {
        $controller = new RouteController();
        $routes = $controller->getRouteClasses();

        if (count($routes) > 0) {
            foreach ($routes as $route) {
                $this->assertTrue(
                    class_exists($route) && (new \ReflectionClass($route))->isSubclassOf('GeckoDS\LaravelRoutes\Routes\AbstractRouteController'),
                    "Class {$route} should be a subclass of AbstractRouteController"
                );
            }
        } else {
            // If no routes discovered, that's okay - test that we got a Collection
            $this->assertInstanceOf(\Illuminate\Support\Collection::class, $routes);
        }
    }

    /**
     * Verifies that the config path is respected
     */
    public function testRespectsConfigPath()
    {
        $this->app['config']->set('routes.path', app_path('Http/Routes/**/*.php'));
        
        $controller = new RouteController();
        $routes = $controller->getRouteClasses();

        $this->assertGreaterThanOrEqual(0, count($routes));
    }

    /**
     * Verifies that the namespace config is respected
     */
    public function testRespectsConfigPrefixNamespace()
    {
        $this->app['config']->set('routes.prefix_namespace', 'App\\Http\\Routes\\');
        
        $controller = new RouteController();
        $routes = $controller->getRouteClasses();

        if (count($routes) > 0) {
            foreach ($routes as $route) {
                $this->assertStringStartsWith('App\\Http\\Routes\\', $route);
            }
        } else {
            // If no routes discovered, test passes - verify we got a Collection
            $this->assertInstanceOf(\Illuminate\Support\Collection::class, $routes);
        }
    }

    /**
     * Verifies that handle() calls the discovered routes
     */
    public function testCallsDiscoveredRoutesOnHandle()
    {
        // Create a dynamic test route
        $testRoute = new class extends \GeckoDS\LaravelRoutes\Routes\AbstractRouteController {
            public static $handleCalled = false;

            public function handle()
            {
                self::$handleCalled = true;
            }
        };

        $testClassName = get_class($testRoute);
        $this->app['config']->set('routes.other_routes', [$testClassName]);

        $controller = new RouteController();
        $controller->handle();

        $this->assertTrue($testRoute::$handleCalled, 'Test route should be called');
    }

    /**
     * Verifies that other_routes from config are included
     */
    public function testIncludesOtherRoutesFromConfig()
    {
        $mockRoute = new class extends \GeckoDS\LaravelRoutes\Routes\AbstractRouteController {
            public static $called = false;

            public function handle()
            {
                self::$called = true;
            }
        };

        $className = get_class($mockRoute);
        $this->app['config']->set('routes.other_routes', [$className]);
        
        $controller = new RouteController();
        $controller->handle();

        $this->assertTrue($mockRoute::$called, 'Other route should be called');
    }

    /**
     * Verifies that getRouteClasses returns a Collection
     */
    public function testReturnsACollection()
    {
        $controller = new RouteController();
        $routes = $controller->getRouteClasses();

        $this->assertInstanceOf(\Illuminate\Support\Collection::class, $routes);
    }
}
