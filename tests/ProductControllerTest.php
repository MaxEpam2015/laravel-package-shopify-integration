<?php

namespace Max\ShopifyIntegration\Tests;

use Illuminate\Support\Facades\Http;
use Max\ShopifyIntegration\Models\ShopifyStore;

class ProductControllerTest extends TestCase
{
    public function test_it_returns_products_for_a_connected_shop(): void
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
}
