<?php

declare(strict_types=1);

namespace Max\ShopifyIntegration\Http\Controllers;

use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Routing\Controller;
use Max\ShopifyIntegration\Services\ProductService;

class ProductController extends Controller
{

    public function __construct(protected ProductService $service)
    {
    }

    public function index(Request $request): JsonResponse
    {
        $shop = $request->query('shop');

        if (! $shop) {
            return response()->json(['error' => 'Missing ?shop parameter'], 400);
        }

        try {
            $products = $this->service->getProductsForShop($shop);
            return response()->json($products, 200);
        } catch (\Exception $e) {
            return response()->json([
                'error' => 'Failed to fetch products',
                'message' => $e->getMessage(),
            ], $e->getCode() ?: 500);
        }
    }
}
