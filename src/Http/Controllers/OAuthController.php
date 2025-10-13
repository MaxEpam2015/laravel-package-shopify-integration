<?php

declare(strict_types=1);

namespace Max\ShopifyIntegration\Http\Controllers;

use Illuminate\Http\JsonResponse;
use Illuminate\Routing\Controller;
use Max\ShopifyIntegration\Http\Requests\ShopifyInstallRequest;
use Max\ShopifyIntegration\Http\Requests\ShopifyCallbackRequest;
use Max\ShopifyIntegration\Services\OAuthService;
use Illuminate\Http\RedirectResponse;
class OAuthController extends Controller
{
    public function __construct(protected OAuthService $service)
    {
    }

    public function install(ShopifyInstallRequest $request): RedirectResponse
    {
        $url = $this->service->buildInstallUrl($request->shop);
        return redirect()->away($url);
    }

    public function callback(ShopifyCallbackRequest $request): JsonResponse
    {
        $result = $this->service->handleCallback($request->validated());

        return response()->json(
            ['message' => $result['message'], 'shop' => $result['shop'] ?? null],
            $result['status']
        );
    }
}
