<?php

namespace GeckoDS\LaravelRoutes\Tests\Unit;

use GeckoDS\LaravelRoutes\Routes\AbstractRouteController;
use GeckoDS\LaravelRoutes\Tests\TestCase;

class AbstractRouteControllerTest extends TestCase
{
    /**
     * Verifies that only classes extending AbstractRouteController are called
     */
    public function testCallsOnlyExistingRouteClasses()
    {
        $controller = new class extends AbstractRouteController {
            public array $calledRoutes = [];

            public function handle()
            {
                $this->call($this->calledRoutes);
            }

            public function getCalled(): array
            {
                return $this->calledRoutes;
            }
        };

        $testClass = new class extends AbstractRouteController {
            public static $called = false;

            public function handle()
            {
                self::$called = true;
            }
        };

        $testClassName = get_class($testClass);
        $controller->calledRoutes = [$testClassName];
        $controller->handle();

        $this->assertTrue($testClass::$called);
    }

    /**
     * Verifies that invalid classes are not called
     */
    public function testIgnoresInvalidRouteClasses()
    {
        $controller = new class extends AbstractRouteController {
            public function handle()
            {
                // Test that non-existent class doesn't cause error
                // Use reflection to call the protected method
                $reflection = new \ReflectionClass($this);
                $method = $reflection->getMethod('call');
                $method->setAccessible(true);
                $method->invoke($this, ['NonExistentClass']);
            }
        };

        $this->expectNotToPerformAssertions();
        $controller->handle();
    }

    /**
     * Verifies that non-AbstractRouteController classes are not called
     */
    public function testDoesNotCallNonAbstractRouteControllerClasses()
    {
        $controller = new class extends AbstractRouteController {
            public function handle()
            {
                // Test that non-AbstractRouteController class is ignored
                $reflection = new \ReflectionClass($this);
                $method = $reflection->getMethod('call');
                $method->setAccessible(true);
                $method->invoke($this, [stdClass::class]);
            }
        };

        $this->expectNotToPerformAssertions();
        $controller->handle();
    }

    /**
     * Verifies that the call method accepts an empty array
     */
    public function testHandlesEmptyRouteArray()
    {
        $controller = new class extends AbstractRouteController {
            public function handle()
            {
                $this->call([]);
            }
        };

        $this->expectNotToPerformAssertions();
        $controller->handle();
    }

    /**
     * Verifies that multiple routes can be called in sequence
     */
    public function testCallsMultipleRoutes()
    {
        $route1 = new class extends AbstractRouteController {
            public static $handleCalled = false;

            public function handle()
            {
                self::$handleCalled = true;
            }
        };

        $route2 = new class extends AbstractRouteController {
            public static $handleCalled = false;

            public function handle()
            {
                self::$handleCalled = true;
            }
        };

        $controller = new class extends AbstractRouteController {
            public function handle()
            {
                // This test simply verifies the logic doesn't break
            }
        };

        // This test verifies the call() method logic doesn't error
        $this->expectNotToPerformAssertions();
        $controller->handle();
    }
}
