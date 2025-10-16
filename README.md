# Laravel <img src="https://upload.wikimedia.org/wikipedia/commons/9/9a/Laravel.svg" alt="Laravel" width="25" height="25"/> + Shopify <img src="https://cdn-icons-png.flaticon.com/512/5968/5968887.png" alt="Shopify" width="25" height="25"/>  Integration Package

### A modern, production-ready Laravel package for connecting your app to Shopify with full OAuth authentication, multi-store support, and Laravel integration.

🚀 Why Use This Package

This package provides:
✅ Deep Laravel integration (service providers, routers, requests, services, tests, middleware, artisan command, migration)
🔐 Production-ready OAuth authentication and shop session management
🧑‍💼 Multi-store token handling
🧰 Easy access to Shopify REST and GraphQL APIs
🧩 Configuration, migrations, and routing out of the box
🧪 Testable, extensible architecture for your own Shopify apps

With this package, you spend less time writing boilerplate and more time building features merchants love.

⚙️ Prerequisites

Before you begin, make sure you have:

* PHP 8.3+
* Laravel 10+
* Composer installed
* A [Shopify Partner Account](https://partners.shopify.com/)
to create your app and get credentials
* A Shopify development store for testing

* [ngrok](https://ngrok.com/) for HTTPS tunneling during local OAuth testing

### 🧩 Step 1: Install the Package
From your Laravel project root:
```
composer require maxepam2015/shopify-integration
```

If developing locally via path repository, ensure your main project composer.json includes:
```
"repositories": [
  {
    "type": "path",
    "url": "packages/Max/ShopifyIntegration"
  }
]

```
### ⚙️ Step 2: Publish Config and Migration Files
```
php artisan vendor:publish --provider="Max\ShopifyIntegration\ShopifyServiceProvider"
```
This publishes:
* config/shopify.php
* database/migrations/2025_10_09_856483_create_shopify_stores_table.php
### 🗄️ Step 3: Run the Migrations
```
php artisan migrate
```
This creates the shopify_stores table used to store connected shops and their OAuth tokens.
### 🔑 Step 4: Configure Your App Credentials
Edit your .env file:
```dotenv
SHOPIFY_API_KEY=your-shopify-api-key
SHOPIFY_API_SECRET=your-shopify-api-secret
SHOPIFY_API_SCOPES=read_products,write_products
SHOPIFY_REDIRECT_URI=https://your-ngrok-url.ngrok-free.dev/api/shopify/callback
SHOPIFY_API_VERSION=2025-01
```
make sure your settings match your Shopify app dashboard (especially API Key, Secret, Scopes, Redirect URLs).

### 🧭 Step 5: Update Your Shopify App URLs

In your Shopify Partner Dashboard → App setup:

| Field | Value |
| :--- | :--- |
| **App URL** | `https://your-ngrok-url.ngrok-free.dev` |
| **Allowed redirection URL(s)** | `https://your-ngrok-url.ngrok-free.dev/api/shopify/callback` |

### 🛠️ Step 6: OAuth Flow (No /install Route)
Merchants (or you, for your dev store) can install the app manually using the direct OAuth URL below:
```
https://your-store.myshopify.com/admin/oauth/authorize?client_id={SHOPIFY_API_KEY}&scope={SHOPIFY_SCOPES}&redirect_uri={SHOPIFY_REDIRECT_URI}
```
When authorization completes, Shopify redirects to:
```
https://your-ngrok-url.ngrok-free.dev/api/shopify/callback?code=xxxx&shop=your-store.myshopify.com
```
Your package handles this automatically and securely stores the access token in the shopify_stores table.

### 🧩 Step 7: Access Connected Shops via Model
The package includes a ready-to-use Eloquent model:
```php
use Max\ShopifyIntegration\Models\ShopifyStore;

$store = ShopifyStore::firstWhere('shop', 'your-store.myshopify.com');
$token = $store->access_token;
```

### 🌐 Step 8: Fetch Products via API
Once the shop is connected, you can call the /api/shopify/products endpoint:
```
GET /api/shopify/products?shop=your-store.myshopify.com
```
Response example:
```json
{
  "products": [
    { "id": 123456789, "title": "T-Shirt", "price": "29.99" },
    { "id": 987654321, "title": "Hat", "price": "19.99" }
  ]
}
```
**Under the hood**
ShopifyClient dynamically loads the shop’s token and sends the request:
```php
use Max\ShopifyIntegration\Services\ShopifyClient;

$shopify = new ShopifyClient('your-store.myshopify.com');
$products = $shopify->getProducts();
```
### 🔐 Step 9: Middleware Protection (Optional)
You can protect your routes to ensure only connected stores access them:
```php
Route::middleware(['auth.shopify'])->group(function () {
    Route::get('/dashboard', [DashboardController::class, 'index']);
});

```
### 🧠 Step 10: Testing Locally with ngrok
For development, run:
```
php artisan serve
ngrok http 8000
```
Use the generated HTTPS URL (e.g., https://your-app.ngrok-free.dev) in your .env and Shopify App setup.
Then open:
```
https://your-store.myshopify.com/admin/oauth/authorize?client_id=your-key&scope=read_products&redirect_uri=https://your-app.ngrok-free.dev/api/shopify/callback
```
and approve the permissions.
After the redirect, your Laravel app will automatically store the access token.

### 🧩 Directory structure
```
ShopifyIntegration/
├── config/shopify.php
├── database/migrations/2025_10_09_856483_create_shopify_stores_table.php
├── routes/api.php
├── src/
│   ├── Models/ShopifyStore.php
│   ├── Services/
│   │   ├── ShopifyClient.php
│   │   ├── OAuthService.php
│   │   └── OAuthService.php
│   ├── Http/
│   │   └── Controllers/
│   │       ├── OAuthController.php
│   │       └── ProductController.php
│   │   └── Requests/
│   │       ├── ShopifyCallbackRequest.php
│   │       └── ShopifyInstallRequest.php
│   │   └── Middleware/
│   │       └── AuthenticateShopify.php
│   └── ShopifyServiceProvider.php
├── tests/
│   ├── TestCase.php
│   ├── OAuthControllerTest.php
│   ├── ShopifyClientTest
│   └── ProductControllerTest.php
├── composer.json
├── phpunit.xml
└── README.md
```

### 🧪 Testing
#### Option 1: Run Only the Package Tests
```
vendor/bin/phpunit
```
#### Option 2: run Tests by docker (Package + App)
```
docker compose up
```
From your Laravel project root:
### 🤝 Contributing
Contributions are welcome!
Please see [CONTRIBUTING.md](CONTRIBUTING.md) for details.

Make sure your PR includes:
* Tests for new or changed functionality
* Clear commit messages
* Code that follows PSR-12 formatting guidelines

### 📄 License
The MIT License (MIT). Please see [License File](LICENSE.md) for more information.
