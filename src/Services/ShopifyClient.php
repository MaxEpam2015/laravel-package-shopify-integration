<?php

declare(strict_types=1);

namespace Max\ShopifyIntegration\Services;

use Illuminate\Support\Facades\Http;
use Max\ShopifyIntegration\Models\ShopifyStore;

class ShopifyClient
{
    protected string $shop;
    protected string $token;

    public function __construct(string $shop)
    {
        $this->shop = $shop;

        $store = ShopifyStore::firstWhere('shop', $shop);

        if (! $store) {
            throw new \Exception("Shop not connected: {$shop}");
        }

        $this->token = $store->access_token;
    }

    /**
     * Fetch products from the connected Shopify store.
     */
    public function getProducts(int $limit = 10): array
    {
        $apiVersion = config('shopify.api_version', '2025-01');
        $url = "https://{$this->shop}/admin/api/{$apiVersion}/products.json?limit={$limit}";

        $response = Http::withHeaders([
            'X-Shopify-Access-Token' => $this->token,
            'Content-Type' => 'application/json',
        ])->get($url);

        if ($response->failed()) {
            throw new \Exception("Shopify API error: " . $response->body());
        }

        return $response->json();
    }
}
