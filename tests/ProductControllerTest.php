<?php

namespace Max\ShopifyIntegration\Tests;

use Illuminate\Support\Facades\Http;
use Max\ShopifyIntegration\Models\ShopifyStore;

class ProductControllerTest extends TestCase
{
    protected function setUp(): void
    {
        parent::setUp();

        $this->withoutMiddleware();

    }

    public function test_returns_products_for_a_connected_shop(): void
    {
        ShopifyStore::create([
            'shop' => 'test-shop.myshopify.com',
            'access_token' => 'token123',
        ]);

        Http::fake([
            'https://test-shop.myshopify.com/admin/api/*/products.json*' => Http::response([
                'products' => [
                    ['id' => 1, 'title' => 'Fake Product'],
                ],
            ]),
        ]);

        $response = $this->getJson('/api/shopify/products?shop=test-shop.myshopify.com');

        $response->assertOk();
        $response->assertJsonFragment(['title' => 'Fake Product']);
    }

    public function test_returns_500_if_shopify_api_fails(): void
    {
        ShopifyStore::create([
            'shop' => 'test-shop.myshopify.com',
            'access_token' => 'invalid',
        ]);

        Http::fake([
            '*' => Http::response(['error' => 'Unauthorized'], 401),
        ]);

        $response = $this->getJson('/api/shopify/products?shop=test-shop.myshopify.com');

        $response->assertStatus(500)
            ->assertJsonFragment(['error' => 'Failed to fetch products']);
    }
}
