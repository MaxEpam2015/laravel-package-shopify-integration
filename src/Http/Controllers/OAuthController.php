<?php

declare(strict_types=1);

namespace Max\ShopifyIntegration\Http\Controllers;

use Illuminate\Http\JsonResponse;
use Illuminate\Http\RedirectResponse;
use Illuminate\Routing\Controller;
use Max\ShopifyIntegration\Http\Requests\OAuth\CallbackRequest;
use Max\ShopifyIntegration\Http\Requests\OAuth\InstallRequest;
use Max\ShopifyIntegration\Services\OAuthService;

class OAuthController extends Controller
{
    public function __construct(protected OAuthService $service)
    {
    }

    public function install(InstallRequest $request): RedirectResponse
    {
        $url = $this->service->buildInstallUrl($request->shop);
        return redirect()->away($url);
    }

    public function callback(CallbackRequest $request): JsonResponse
    {
        $result = $this->service->handleCallback($request->validated());

        return response()->json(
            ['message' => $result['message'], 'shop' => $result['shop'] ?? null],
            $result['status']
        );
    }
}
