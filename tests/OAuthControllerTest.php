<?php

namespace Max\ShopifyIntegration\Tests;

use Illuminate\Support\Facades\Http;

class OAuthControllerTest extends TestCase
{

    public function test_validates_shop_domain_format()
    {
        $response = $this->get('/api/shopify/install?shop=invalid-shop.com');

        $response->assertStatus(302); // Laravel redirects on validation errors by default
        $this->assertStringContainsString('shop', session('errors')->first('shop'));
    }

    public function test_redirects_to_shopify_authorize_url_with_correct_parameters()
    {
        config()->set('shopify.key', 'test-api-key');
        config()->set('shopify.scopes', 'read_products,write_products');
        config()->set('shopify.redirect_uri', 'https://example.ngrok-free.dev/api/shopify/callback');

        $shop = 'my-test-shop.myshopify.com';

        $response = $this->get("/api/shopify/install?shop={$shop}");

        $expectedUrl = sprintf(
            'https://%s/admin/oauth/authorize?client_id=%s&scope=%s&redirect_uri=%s',
            $shop,
            'test-api-key',
            'read_products,write_products',
            urlencode('https://example.ngrok-free.dev/api/shopify/callback')
        );

        $response->assertRedirect($expectedUrl);
    }

    public function test_stores_a_shop_and_token_on_oauth_callback(): void
    {
        Http::fake([
            'test-shop.myshopify.com/admin/oauth/access_token' => Http::response([
                'access_token' => 'fake-access-token',
            ], 200),
        ]);

        $params = [
            'shop' => 'test-shop.myshopify.com',
            'code' => '123',
        ];

        // Compute HMAC the same way Shopify does
        ksort($params);
        $queryString = urldecode(http_build_query($params));
        $hmac = hash_hmac('sha256', $queryString, config('shopify.secret'));

        // Build full query string with hmac param
        $query = http_build_query(array_merge($params, ['hmac' => $hmac]));

        $response = $this->getJson("/api/shopify/callback?{$query}");

        $response->assertStatus(200);

        $this->assertDatabaseHas('shopify_stores', [
            'shop' => 'test-shop.myshopify.com',
            'access_token' => 'fake-access-token',
        ]);
    }

    public function test_redirects_to_shopify_oauth_url(): void
    {
        config()->set('shopify.key', 'test-api-key');
        config()->set('shopify.scopes', 'read_products');
        config()->set('shopify.redirect_uri', 'https://example.ngrok-free.dev/api/shopify/callback');

        $shop = 'my-test-shop.myshopify.com';

        $response = $this->get("/api/shopify/install?shop={$shop}");

        $expectedUrl = sprintf(
            'https://%s/admin/oauth/authorize?client_id=%s&scope=%s&redirect_uri=%s',
            $shop,
            'test-api-key',
            'read_products',
            urlencode('https://example.ngrok-free.dev/api/shopify/callback')
        );

        $response->assertRedirect($expectedUrl);
    }

    public function test_fails_for_invalid_shop_domain(): void
    {
        $response = $this->get('/api/shopify/install?shop=invalid.com');

        $response->assertSessionHasErrors(['shop']);
    }
}
