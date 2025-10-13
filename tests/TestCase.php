<?php

declare(strict_types=1);

namespace Max\ShopifyIntegration\Tests;

use Orchestra\Testbench\TestCase as BaseTestCase;
use Max\ShopifyIntegration\ShopifyServiceProvider;

abstract class TestCase extends BaseTestCase
{
    protected function getPackageProviders($app)
    {
        return [
            ShopifyServiceProvider::class,
        ];
    }

    protected function defineEnvironment($app)
    {
        // Configure database to use in-memory SQLite for testing
        $app['config']->set('database.default', 'sqlite');
        $app['config']->set('database.connections.sqlite', [
            'driver' => 'sqlite',
            'database' => ':memory:',
            'prefix' => '',
        ]);

        // Add fake Shopify credentials for OAuth tests
        $app['config']->set('shopify.key', 'test-key');
        $app['config']->set('shopify.secret', 'test-secret');
    }

    protected function setUp(): void
    {
        parent::setUp();

        // Run your package migrations automatically
        $this->loadMigrationsFrom(__DIR__.'/../database/migrations');
    }
}
