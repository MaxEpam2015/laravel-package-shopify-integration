<?php

declare(strict_types=1);

namespace Max\ShopifyIntegration\Http\Requests;


use Illuminate\Foundation\Http\FormRequest;

class ShopifyInstallRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        return [
            'shop' => ['required', 'string', 'regex:/^[a-zA-Z0-9][a-zA-Z0-9\-]*\.myshopify\.com$/'],
        ];
    }

    public function messages(): array
    {
        return [
            'shop.required' => 'The ?shop parameter is required.',
            'shop.regex' => 'The shop parameter must be a valid Shopify domain (e.g., my-store.myshopify.com).',
        ];
    }
}
