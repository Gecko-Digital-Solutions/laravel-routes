<?php

namespace GeckoDS\LaravelRoutes\Tests\Feature;

use GeckoDS\LaravelRoutes\Tests\TestCase;

class ConfigPublishTest extends TestCase
{
    /**
     * Verifies that config can be accessed via the config() helper
     */
    public function testCanAccessConfigViaHelper()
    {
        $this->assertIsArray(config('routes'));
        $this->assertArrayHasKey('path', config('routes'));
        $this->assertArrayHasKey('prefix_namespace', config('routes'));
        $this->assertArrayHasKey('other_routes', config('routes'));
    }

    /**
     * Verifies that config can be modified
     */
    public function testAllowsConfigModification()
    {
        $originalPath = config('routes.path');
        
        config(['routes.path' => 'custom/path/**/*.php']);
        
        $this->assertEquals('custom/path/**/*.php', config('routes.path'));
        
        // Restore
        config(['routes.path' => $originalPath]);
    }

    /**
     * Verifies that other_routes can be set to empty
     */
    public function testHandlesEmptyOtherRoutes()
    {
        config(['routes.other_routes' => []]);
        
        $this->assertEquals([], config('routes.other_routes'));
    }

    /**
     * Verifies that other_routes can accept class names
     */
    public function testHandlesOtherRoutesWithClassNames()
    {
        $mockClass = 'App\\Routes\\CustomRoute';
        config(['routes.other_routes' => [$mockClass]]);
        
        $this->assertContains($mockClass, config('routes.other_routes'));
    }
}
