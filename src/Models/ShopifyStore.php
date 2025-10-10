<?php

declare(strict_types=1);

namespace Max\ShopifyIntegration\Models;

use Illuminate\Database\Eloquent\Model;

class ShopifyStore extends Model
{
    protected $table = 'shopify_stores';

    protected $fillable = [
        'shop',
        'access_token',
    ];
}
