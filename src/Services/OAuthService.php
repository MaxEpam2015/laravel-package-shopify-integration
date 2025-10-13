<?php

declare(strict_types=1);

namespace Max\ShopifyIntegration\Services;

use Illuminate\Support\Facades\Http;
use Max\ShopifyIntegration\Models\ShopifyStore;

class OAuthService
{
    /**
     * Build the Shopify OAuth install URL for a given shop.
     */
    public function buildInstallUrl(string $shop): string
    {
        $scopes = config('shopify.scopes');
        $redirectUri = config('shopify.redirect_uri');
        $clientId = config('shopify.key');

        return sprintf(
            'https://%s/admin/oauth/authorize?client_id=%s&scope=%s&redirect_uri=%s',
            $shop,
            $clientId,
            $scopes,
            urlencode($redirectUri)
        );
    }

    /**
     * Exchange a temporary code for a permanent access token.
     */
    public function handleCallback(array $data): array
    {
        $shop = $data['shop'];
        $code = $data['code'];
        $hmac = $data['hmac'];

        $params = collect($data)->except(['signature', 'hmac'])->sortKeys();
        $computedHmac = hash_hmac(
            'sha256',
            urldecode($params->map(fn ($value, $key) => "$key=$value")->implode('&')),
            config('shopify.secret')
        );

        if (! hash_equals($hmac, $computedHmac)) {
            return [
                'status' => 400,
                'message' => 'Invalid HMAC validation',
            ];
        }

        $response = Http::asForm()->post("https://{$shop}/admin/oauth/access_token", [
            'client_id' => config('shopify.key'),
            'client_secret' => config('shopify.secret'),
            'code' => $code,
        ]);

        if ($response->failed()) {
            return [
                'status' => 500,
                'message' => 'Token request failed',
            ];
        }

        $accessToken = $response->json('access_token');

        ShopifyStore::updateOrCreate(
            ['shop' => $shop],
            ['access_token' => $accessToken]
        );

        return [
            'status' => 200,
            'message' => 'Shop connected successfully!',
            'shop' => $shop,
        ];
    }
}
