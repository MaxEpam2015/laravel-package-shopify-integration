<?php

declare(strict_types=1);

namespace Max\ShopifyIntegration\Http\Controllers;


use Illuminate\Http\Request;
use Illuminate\Routing\Controller;
use Illuminate\Support\Facades\Http;
use Max\ShopifyIntegration\Models\ShopifyStore;

class OAuthController extends Controller
{

    public function callback(Request $request)
    {
        $shop = $request->get('shop');
        $code = $request->get('code');
        $hmac = $request->get('hmac');

        if (!$shop || !$code || !$hmac) {
            return response('Invalid request parameters', 400);
        }

        // Validate HMAC
        $params = $request->except(['signature', 'hmac']);
        ksort($params);
        $computedHmac = hash_hmac('sha256', urldecode(http_build_query($params)), config('shopify.secret'));

        if (!hash_equals($hmac, $computedHmac)) {
            return response('Invalid HMAC validation', 400);
        }

        // Exchange temporary code for a permanent access token
        $response = Http::asForm()->post("https://{$shop}/admin/oauth/access_token", [
            'client_id' => config('shopify.key'),
            'client_secret' => config('shopify.secret'),
            'code' => $code,
        ]);

        if ($response->failed()) {
            return response('Token request failed', 500);
        }

        $accessToken = $response->json('access_token');

        ShopifyStore::updateOrCreate(
            ['shop' => $shop],
            ['access_token' => $accessToken]
        );

        return response()->json([
            'message' => 'Shop connected successfully!',
            'shop' => $shop,
        ]);
    }
}
