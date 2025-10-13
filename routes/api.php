<?php

use Illuminate\Support\Facades\Route;
use Max\ShopifyIntegration\Http\Controllers\OAuthController;
use Max\ShopifyIntegration\Http\Controllers\ProductController;

Route::prefix('api/shopify')->group(function () {
    Route::get('/install', [OAuthController::class, 'install']);
    Route::get('/callback', [OAuthController::class, 'callback']);
    Route::middleware(['auth.shopify'])->get('/products', [ProductController::class, 'index']);
});
