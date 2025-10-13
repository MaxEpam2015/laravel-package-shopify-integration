<?php

declare(strict_types=1);

namespace Max\ShopifyIntegration\Tests;

use Illuminate\Support\Facades\Http;
use Max\ShopifyIntegration\Models\ShopifyStore;
use Max\ShopifyIntegration\Services\ShopifyClient;

class ShopifyClientTest extends TestCase
{
    public function test_fetches_products_for_a_connected_shop(): void
    {
        ShopifyStore::create([
            'shop' => 'test-shop.myshopify.com',
            'access_token' => 'valid_token_123',
        ]);

        Http::fake([
            'https://test-shop.myshopify.com/admin/api/*/products.json*' => Http::response([
                'products' => [
                    ['id' => 1, 'title' => 'Test Product'],
                    ['id' => 2, 'title' => 'Another Product'],
                ],
            ], 200),
        ]);

        $client = new ShopifyClient('test-shop.myshopify.com');
        $products = $client->getProducts();

        $this->assertIsArray($products);
        $this->assertCount(2, $products['products']);
        $this->assertEquals('Test Product', $products['products'][0]['title']);
    }

    public function test_throws_exception_if_shop_not_connected(): void
    {
        $this->expectException(\Exception::class);
        $this->expectExceptionMessage('Shop not connected: unknown-shop.myshopify.com');

        new ShopifyClient('unknown-shop.myshopify.com');
    }

    public function test_throws_exception_if_api_request_fails(): void
    {
        ShopifyStore::create([
            'shop' => 'bad-shop.myshopify.com',
            'access_token' => 'invalid_token',
        ]);

        Http::fake([
            'https://bad-shop.myshopify.com/admin/api/*/products.json*' => Http::response([
                'errors' => 'Unauthorized',
            ], 401),
        ]);

        $client = new ShopifyClient('bad-shop.myshopify.com');

        $this->expectException(\Exception::class);
        $this->expectExceptionMessage('Shopify API error');

        $client->getProducts();
    }
}
