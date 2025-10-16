<?php

declare(strict_types=1);

namespace Max\ShopifyIntegration\Services;
use Max\ShopifyIntegration\Models\ShopifyStore;

class ProductService
{
    public function getProductsForShop(string $shop): array
    {
        $store = ShopifyStore::firstWhere('shop', $shop);

        if (! $store) {
            throw new \Exception('Shop not connected. Please complete OAuth.');
        }

        $client = new ShopifyClient($shop);

        return $client->getProducts();
    }
}
