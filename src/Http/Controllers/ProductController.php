<?php

namespace Max\ShopifyIntegration\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Routing\Controller;
use Max\ShopifyIntegration\Models\ShopifyStore;
use Max\ShopifyIntegration\Services\ShopifyClient;

class ProductController extends Controller
{
    public function index(Request $request)
    {
        $shop = $request->query('shop');

        if (! $shop) {
            return response()->json(['error' => 'Missing ?shop parameter'], 400);
        }

        $store = ShopifyStore::where('shop', $shop)->first();

        if (! $store) {
            return response()->json(['error' => 'Shop not connected. Please complete OAuth.'], 401);
        }

        try {
            $client = new ShopifyClient($store->shop);
            $products = $client->getProducts();

            return response()->json($products, 200);
        } catch (\Exception $e) {
            return response()->json([
                'error' => 'Failed to fetch products',
                'message' => $e->getMessage(),
            ], 500);
        }
    }
}
