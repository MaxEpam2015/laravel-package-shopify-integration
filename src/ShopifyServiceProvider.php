<?php

declare(strict_types=1);

namespace Max\ShopifyIntegration;

use Illuminate\Support\ServiceProvider;
use Max\ShopifyIntegration\Commands\FetchProducts;
use Max\ShopifyIntegration\Http\Middleware\AuthenticateShopify;

class ShopifyServiceProvider extends ServiceProvider
{
    public function register()
    {
        $this->mergeConfigFrom(__DIR__.'/../config/shopify.php', 'shopify');
    }

    public function boot()
    {
        $this->publishes([
            __DIR__.'/../config/shopify.php' => config_path('shopify.php'),
        ], 'config');

        $this->mergeConfigFrom(
            __DIR__.'/../config/shopify.php',
            'shopify'
        );

        $this->loadRoutesFrom(__DIR__.'/../routes/api.php');

        if ($this->app->runningInConsole()) {
            $this->commands([
                FetchProducts::class,
            ]);
        }

        $this->loadMigrationsFrom(__DIR__ . '/../database/migrations');
        $this->app['router']->aliasMiddleware('auth.shopify', AuthenticateShopify::class);

    }
}
