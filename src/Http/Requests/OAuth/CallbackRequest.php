<?php

declare(strict_types=1);

namespace Max\ShopifyIntegration\Http\Requests\OAuth;

use Illuminate\Foundation\Http\FormRequest;

class CallbackRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        return [
            'shop' => ['required', 'string', 'regex:/^[a-zA-Z0-9][a-zA-Z0-9\-]*\.myshopify\.com$/'],
            'code' => ['required', 'string'],
            'hmac' => ['required', 'string'],
        ];
    }

    public function messages(): array
    {
        return [
            'shop.regex' => 'Invalid shop domain format.',
        ];
    }
}
