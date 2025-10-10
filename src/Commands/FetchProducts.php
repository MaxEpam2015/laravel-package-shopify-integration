<?php

declare(strict_types=1);
namespace Max\ShopifyIntegration\Commands;

use Illuminate\Console\Command;
use Max\ShopifyIntegration\Services\ShopifyClient;

class FetchProducts extends Command
{
    protected $signature = 'shopify:fetch-products {--limit=10}';
    protected $description = 'Fetch products from Shopify';

    public function handle(ShopifyClient $shopify)
    {
        $products = $shopify->getProducts((int) $this->option('limit'));
        $this->info('Fetched ' . count($products['products'] ?? []) . ' products.');
    }
}
