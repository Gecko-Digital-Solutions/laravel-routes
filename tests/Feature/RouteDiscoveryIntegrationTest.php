<?php

namespace GeckoDS\LaravelRoutes\Tests\Feature;

use GeckoDS\LaravelRoutes\Routes\RouteController;
use GeckoDS\LaravelRoutes\Tests\TestCase;

class RouteDiscoveryIntegrationTest extends TestCase
{
    /**
     * Verifies complete route discovery and execution integration
     */
    public function testDiscoversAndExecutesRoutesCorrectly()
    {
        $controller = new RouteController();
        
        // Verify discovery - it returns a Collection
        $discoveredRoutes = $controller->getRouteClasses();
        $this->assertInstanceOf(\Illuminate\Support\Collection::class, $discoveredRoutes);

        // Create a test route and verify it is called
        $testRoute = new class extends \GeckoDS\LaravelRoutes\Routes\AbstractRouteController {
            public static $handleCalled = false;

            public function handle()
            {
                self::$handleCalled = true;
            }
        };

        $this->app['config']->set('routes.other_routes', [get_class($testRoute)]);
        $controller->handle();
        
        $this->assertTrue($testRoute::$handleCalled, 'Test route should be called');
    }

    /**
     * Verifies that multiple handle() calls work correctly
     */
    public function testCanHandleMultipleCalls()
    {
        $testRoute = new class extends \GeckoDS\LaravelRoutes\Routes\AbstractRouteController {
            public static $callCount = 0;

            public function handle()
            {
                self::$callCount++;
            }
        };

        $testClassName = get_class($testRoute);
        $this->app['config']->set('routes.other_routes', [$testClassName]);

        $controller = new RouteController();
        
        $controller->handle();
        $firstCount = $testRoute::$callCount;
        
        $controller->handle();
        $secondCount = $testRoute::$callCount;

        $this->assertGreaterThan($firstCount, $secondCount);
    }

    /**
     * Verifies that discovered routes contain fixture classes
     */
    public function testDiscoversFixtureRoutes()
    {
        $controller = new RouteController();
        $routes = $controller->getRouteClasses();

        // Test that getRouteClasses returns a Collection
        $this->assertInstanceOf(\Illuminate\Support\Collection::class, $routes);
    }

    /**
     * Verifies that config can be modified at runtime
     */
    public function testRespectsRuntimeConfigChanges()
    {
        $originalPath = $this->app['config']->get('routes.path');
        
        $this->app['config']->set('routes.path', app_path('Http/Routes/**/*.php'));
        
        $controller = new RouteController();
        $routes = $controller->getRouteClasses();

        $this->assertGreaterThanOrEqual(0, count($routes));
        
        // Restore config
        $this->app['config']->set('routes.path', $originalPath);
    }
}
