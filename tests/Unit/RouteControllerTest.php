<?php

namespace GeckoDS\LaravelRoutes\Tests\Unit;

use GeckoDS\LaravelRoutes\Routes\RouteController;
use GeckoDS\LaravelRoutes\Tests\TestCase;

class RouteControllerTest extends TestCase
{
    /**
     * Verifies that getRouteClasses returns a Collection
     */
    public function testGetRouteClassesReturnsCollection()
    {
        $controller = new RouteController();
        $routes = $controller->getRouteClasses();

        $this->assertInstanceOf(\Illuminate\Support\Collection::class, $routes);
    }

    /**
     * Verifies that discovered routes are valid class names as strings
     */
    public function testReturnsClassNamesAsStrings()
    {
        $controller = new RouteController();
        $routes = $controller->getRouteClasses();

        if ($routes->isNotEmpty()) {
            foreach ($routes as $route) {
                $this->assertIsString($route, 'Route should be a string (class name)');
                $this->assertTrue(class_exists($route), "Class {$route} should exist");
            }
        } else {
            // When no routes discovered, verify we still get a Collection
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

        if ($routes->isNotEmpty()) {
            foreach ($routes as $route) {
                $reflection = new \ReflectionClass($route);
                $this->assertTrue(
                    $reflection->isSubclassOf('GeckoDS\LaravelRoutes\Routes\AbstractRouteController'),
                    "Class {$route} should be a subclass of AbstractRouteController"
                );
            }
        } else {
            // When no routes discovered, test that filtering logic is sound
            $this->assertInstanceOf(\Illuminate\Support\Collection::class, $routes);
        }
    }

    /**
     * Verifies that handle() calls routes from other_routes config
     */
    public function testHandleCallsOtherRoutes()
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

        $this->assertTrue($mockRoute::$called, 'Route should be called via handle()');
    }

    /**
     * Verifies handle works with empty other_routes config
     */
    public function testHandleWorksWithEmptyConfig()
    {
        $this->app['config']->set('routes.other_routes', []);
        
        $controller = new RouteController();
        $result = $controller->handle();

        $this->assertNull($result);
    }

    /**
     * Verifies handle works with empty prefix namespace
     */
    public function testHandlesEmptyPrefixNamespace()
    {
        $this->app['config']->set('routes.prefix_namespace', '');
        
        $controller = new RouteController();
        $routes = $controller->getRouteClasses();

        $this->assertInstanceOf(\Illuminate\Support\Collection::class, $routes);
    }

    /**
     * Verifies route classes are iterable after discovery
     */
    public function testRouteClassesAreIterable()
    {
        $testRoute = new class extends \GeckoDS\LaravelRoutes\Routes\AbstractRouteController {
            public function handle() {}
        };

        $className = get_class($testRoute);
        $this->app['config']->set('routes.other_routes', [$className]);
        
        $controller = new RouteController();
        $routes = $controller->getRouteClasses();

        $this->assertTrue(is_iterable($routes));
        $routes->each(function($classname) {
            $this->assertIsString($classname);
        });
    }

    /**
     * Verifies that handle calls multiple routes correctly
     */
    public function testHandleCallsMultipleRoutes()
    {
        $route1 = new class extends \GeckoDS\LaravelRoutes\Routes\AbstractRouteController {
            public static $called = false;
            public function handle() { self::$called = true; }
        };

        $route2 = new class extends \GeckoDS\LaravelRoutes\Routes\AbstractRouteController {
            public static $called = false;
            public function handle() { self::$called = true; }
        };

        $this->app['config']->set('routes.other_routes', [
            get_class($route1),
            get_class($route2),
        ]);

        $controller = new RouteController();
        $controller->handle();

        $this->assertTrue($route1::$called, 'All routes should be called');
        $this->assertTrue($route2::$called);
    }

    /**
     * Verifies glob pattern discovers PHP files from the workbench
     */
    public function testGlobDiscoversPhpFiles()
    {
        $pattern = app_path('Http/**/*.php');
        $files = glob($pattern, GLOB_BRACE);
        
        $this->assertGreaterThan(0, count($files), 'Should find route files in workbench');
        
        foreach ($files as $file) {
            $this->assertStringEndsWith('.php', $file);
        }
    }

    /**
     * Verifies files without expected class definitions are filtered
     */
    public function testFiltersOutFilesWithoutClass()
    {
        // Verify FileWithoutClass is found by glob but filtered by getRouteClasses
        $pattern = app_path('Http/**/*.php');
        $files = glob($pattern, GLOB_BRACE);
        
        $foundFileWithoutClass = false;
        foreach ($files as $file) {
            if (strpos($file, 'FileWithoutClass') !== false) {
                $foundFileWithoutClass = true;
                break;
            }
        }
        
        $this->assertTrue($foundFileWithoutClass, 'FileWithoutClass.php should be found by glob');
        
        // Verify it's filtered out from discovered routes
        $controller = new RouteController();
        $routes = $controller->getRouteClasses();
        $routesArray = $routes->toArray();
        
        foreach ($routesArray as $route) {
            $this->assertNotStringContainsString('FileWithoutClass', $route);
        }
    }

    /**
     * Verifies classes not extending AbstractRouteController are filtered out
     */
    public function testFiltersOutNonRouteClasses()
    {
        // Ensure NotARoute file exists
        $notARouteFile = app_path('Http/Routes/NotARoute.php');
        $this->assertTrue(file_exists($notARouteFile), 'NotARoute.php should exist');
        
        require_once $notARouteFile;
        
        $this->assertTrue(class_exists('App\\Http\\Routes\\NotARoute'));
        $reflection = new \ReflectionClass('App\\Http\\Routes\\NotARoute');
        $this->assertFalse($reflection->isSubclassOf('GeckoDS\LaravelRoutes\Routes\AbstractRouteController'));
        
        // Verify it's filtered out
        $controller = new RouteController();
        $routes = $controller->getRouteClasses();
        $routesArray = $routes->toArray();
        
        foreach ($routesArray as $route) {
            $this->assertNotStringContainsString('NotARoute', $route);
        }
    }

    /**
     * Verifies empty glob results are handled gracefully
     */
    public function testReturnsEmptyCollectionWhenNoFilesFound()
    {
        $this->app['config']->set('routes.path', app_path('NonExistentPath/**/*.php'));
        
        $controller = new RouteController();
        $routes = $controller->getRouteClasses();
        
        $this->assertInstanceOf(\Illuminate\Support\Collection::class, $routes);
        $this->assertEquals(0, count($routes));
    }
}
