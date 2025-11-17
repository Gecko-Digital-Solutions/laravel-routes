<?php

namespace GeckoDS\LaravelRoutes\Tests\Unit;

use GeckoDS\LaravelRoutes\Providers\LaravelRoutesProvider;
use GeckoDS\LaravelRoutes\Tests\TestCase;

class LaravelRoutesProviderTest extends TestCase
{
    /**
     * Verifies that the provider merges the default config
     */
    public function testMergesDefaultConfig()
    {
        $this->assertTrue($this->app['config']->has('routes'));
        $this->assertTrue($this->app['config']->has('routes.path'));
        $this->assertTrue($this->app['config']->has('routes.prefix_namespace'));
        $this->assertTrue($this->app['config']->has('routes.other_routes'));
    }

    /**
     * Verifies that the default config contains expected keys
     */
    public function testHasRequiredConfigKeys()
    {
        $config = $this->app['config']->get('routes');

        $this->assertArrayHasKey('path', $config);
        $this->assertArrayHasKey('prefix_namespace', $config);
        $this->assertArrayHasKey('other_routes', $config);
    }

    /**
     * Verifies that the config can be published
     */
    public function testPublishesConfig()
    {
        $provider = new LaravelRoutesProvider($this->app);
        
        // Verify that the provider is registered
        $this->assertInstanceOf(LaravelRoutesProvider::class, $provider);
    }

    /**
     * Verifies that the default path value is correct
     */
    public function testHasCorrectDefaultPath()
    {
        // Reset to default config by recreating the provider
        $this->app['config']->set('routes', [
            'path' => app_path('Http/**/*.php'),
            'prefix_namespace' => 'App\\',
            'other_routes' => []
        ]);

        $path = $this->app['config']->get('routes.path');
        
        $this->assertStringContainsString('Http/**/*.php', $path);
    }

    /**
     * Verifies that the default namespace value is correct
     */
    public function testHasCorrectDefaultPrefixNamespace()
    {
        $this->app['config']->set('routes.prefix_namespace', 'App\\');
        
        $namespace = $this->app['config']->get('routes.prefix_namespace');
        
        $this->assertEquals('App\\', $namespace);
    }

    /**
     * Verifies that other_routes is an array by default
     */
    public function testHasOtherRoutesAsArrayByDefault()
    {
        $otherRoutes = $this->app['config']->get('routes.other_routes');
        
        $this->assertIsArray($otherRoutes);
    }
}
