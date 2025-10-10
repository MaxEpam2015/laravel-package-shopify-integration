<?php

declare(strict_types=1);

namespace Max\ShopifyIntegration\Http\Middleware;


use Closure;
use Illuminate\Http\Request;
use Max\ShopifyIntegration\Models\ShopifyStore;

class AuthenticateShopify
{
    public function handle(Request $request, Closure $next)
    {
        $shop = $request->query('shop');

        if (! $shop) {
            return response()->json(['error' => 'Missing ?shop parameter'], 400);
        }

        $store = ShopifyStore::where('shop', $shop)->first();

        if (! $store) {
            return response()->json([
                'error' => 'Shop not connected. Please complete the OAuth flow first.'
            ], 401);
        }

        // Attach the shop to the request for easy access
        $request->attributes->set('shopify_store', $store);

        return $next($request);
    }
}
