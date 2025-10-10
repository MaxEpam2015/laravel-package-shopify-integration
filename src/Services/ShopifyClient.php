<?php

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

        $store = ShopifyStore::where('shop', $shop)->first();

        if (! $store) {
            throw new \Exception("Shop not connected: {$shop}");
        }

        $this->token = $store->access_token;
    }

    public function getProducts(int $limit = 10): array
    {
        $url = "https://{$this->shop}/admin/api/2025-10/products.json?limit={$limit}";

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
