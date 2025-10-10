<?php

namespace Max\ShopifyIntegration\Tests;

use Illuminate\Support\Facades\Http;
use Max\ShopifyIntegration\Models\ShopifyStore;

class OAuthControllerTest extends TestCase
{
    public function test_it_stores_a_shop_and_token_on_oauth_callback(): void
    {
        Http::fake([
            'test-shop.myshopify.com/admin/oauth/access_token' => Http::response([
                'access_token' => 'fake-access-token',
            ], 200),
        ]);

        // Request params
        $params = [
            'shop' => 'test-shop.myshopify.com',
            'code' => '123',
        ];

        // ✅ Compute HMAC the same way Shopify does
        ksort($params);
        $queryString = urldecode(http_build_query($params));
        $hmac = hash_hmac('sha256', $queryString, config('shopify.secret'));

        // Build full query string with hmac param
        $query = http_build_query(array_merge($params, ['hmac' => $hmac]));

        // Hit the callback endpoint
        $response = $this->getJson("/api/shopify/callback?{$query}");

        // ✅ Should pass now
        $response->assertStatus(200);

        $this->assertDatabaseHas('shopify_stores', [
            'shop' => 'test-shop.myshopify.com',
            'access_token' => 'fake-access-token',
        ]);
    }
}
