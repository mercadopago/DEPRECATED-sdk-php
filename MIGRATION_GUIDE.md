# Migration Guide: `mercadopago/sdk` → `mercadopago/dx-php`

This guide covers migrating from the deprecated `mercadopago/sdk` (the `MP` class, v0.5.x) to the current `mercadopago/dx-php` (v3.x).

## Requirements

| | Deprecated | Current |
|---|---|---|
| Package | `mercadopago/sdk` | `mercadopago/dx-php` |
| PHP | 5.x / 7.x | **8.2+** |
| Autoloading | Manual `require_once` | Composer PSR-4 |

## Step 1 — Update Your Dependency

```bash
composer remove mercadopago/sdk
composer require "mercadopago/dx-php:^3.0"
```

Replace any `require_once('mercadopago.php')` with the Composer autoloader:

```php
// Before
require_once('mercadopago.php');

// After
require_once 'vendor/autoload.php';
```

---

## Step 2 — SDK Initialization

The deprecated SDK used an instantiated `MP` class that held credentials as instance state. The current SDK uses a static configuration class.

```php
// Before
$mp = new MP("APP_USR-your-access-token");

// After
use MercadoPago\MercadoPagoConfig;

MercadoPagoConfig::setAccessToken("APP_USR-your-access-token");
```

### Client ID + Client Secret (removed)

The `MP("CLIENT_ID", "CLIENT_SECRET")` constructor form that auto-fetched an access token is no longer supported. You must use an access token directly. Obtain one from the [Mercado Pago developer panel](https://www.mercadopago.com/developers/panel/app).

```php
// Before — no longer supported
$mp = new MP("CLIENT_ID", "CLIENT_SECRET");

// After — use access token directly
MercadoPagoConfig::setAccessToken("APP_USR-your-access-token");
```

### Sandbox Mode

```php
// Before
$mp->sandbox_mode(true);

// After
MercadoPagoConfig::setRuntimeEnviroment(MercadoPagoConfig::LOCAL);
```

### Optional Configuration

```php
MercadoPagoConfig::setMaxRetries(3);           // default: 3
MercadoPagoConfig::setRetryDelay(500);         // ms, base delay for exponential backoff
MercadoPagoConfig::setConnectionTimeout(20000); // ms
MercadoPagoConfig::setIntegratorId("...");     // partner tracking ID
```

---

## Step 3 — API Calls

The deprecated SDK exposed a single `MP` object with methods for every resource. The current SDK has **dedicated client classes** per resource, each returning **typed PHP objects** instead of raw arrays.

### Response Format Change

```php
// Before — every response was an array
$result = $mp->get_payment(12345);
echo $result["status"];              // 200
echo $result["response"]["status"];  // "approved"
echo $result["response"]["id"];      // 12345

// After — typed object with public properties
$payment = (new PaymentClient())->get(12345);
echo $payment->status;  // "approved"
echo $payment->id;      // 12345
```

### Payments

```php
use MercadoPago\Client\Payment\PaymentClient;
use MercadoPago\Client\Payment\PaymentRefundClient;
use MercadoPago\Net\MPSearchRequest;

$client = new PaymentClient();

// Create
// Before: $mp->post(["uri" => "/v1/payments", "data" => $data])
$payment = $client->create([
    "transaction_amount" => 100.0,
    "token"              => "<CARD_TOKEN>",
    "payment_method_id"  => "master",
    "installments"       => 1,
    "payer"              => ["email" => "payer@email.com"],
]);

// Get
// Before: $mp->get_payment($id)
$payment = $client->get(12345);

// Cancel
// Before: $mp->cancel_payment($id)
$payment = $client->cancel(12345);

// Capture (new — not available in deprecated SDK)
$payment = $client->capture(12345);          // full capture
$payment = $client->capture(12345, 50.0);    // partial capture

// Search
// Before: $mp->search_payment($filters, $offset, $limit)
$result = $client->search(new MPSearchRequest(
    limit: 10,
    offset: 0,
    filters: ["status" => "approved"]
));
echo $result->total;
foreach ($result->results as $p) { echo $p->id; }
```

### Refunds

Refunds are now handled by a dedicated `PaymentRefundClient`.

```php
use MercadoPago\Client\Payment\PaymentRefundClient;

$refundClient = new PaymentRefundClient();

// Total refund
// Before: $mp->refund_payment($id)
$refund = $refundClient->refundTotal(12345);

// Partial refund (new — not available in deprecated SDK)
$refund = $refundClient->refund(12345, 25.0);

// List all refunds for a payment
$refunds = $refundClient->list(12345);

// Get a specific refund
$refund = $refundClient->get(12345, $refundId);
```

### Preferences (Checkout Pro)

```php
use MercadoPago\Client\Preference\PreferenceClient;

$client = new PreferenceClient();

// Create
// Before: $mp->create_preference($data)
$preference = $client->create([
    "items" => [[
        "title"       => "Product",
        "quantity"    => 1,
        "currency_id" => "USD",
        "unit_price"  => 10.4,
    ]],
]);
echo $preference->id;
echo $preference->init_point; // redirect URL

// Get
// Before: $mp->get_preference($id)
$preference = $client->get("PREF_ID");

// Update
// Before: $mp->update_preference($id, $data)
$preference = $client->update("PREF_ID", $data);
```

### Customers

The deprecated SDK had no dedicated customer methods — calls went through the generic HTTP interface. The current SDK provides `CustomerClient`.

```php
use MercadoPago\Client\Customer\CustomerClient;

$client = new CustomerClient();

// Before: $mp->post(["uri" => "/v1/customers", "data" => ["email" => "..."]])
$customer = $client->create(["email" => "user@email.com", "first_name" => "John"]);

// Convenience method
$customer = $client->createByEmail("user@email.com");

// Before: $mp->get(["uri" => "/v1/customers/$id"])
$customer = $client->get("CUSTOMER_ID");

$customer = $client->update("CUSTOMER_ID", ["first_name" => "Jane"]);
$result   = $client->search(new MPSearchRequest(10, 0, ["email" => "user@email.com"]));
```

### Generic HTTP Calls (removed)

The deprecated `$mp->get()`, `$mp->post()`, `$mp->put()`, `$mp->delete()` escape hatches are gone. All calls go through typed clients. If you need a resource not yet covered, subclass `MercadoPagoClient` and call the protected `send()` method.

---

## Step 4 — Exception Handling

The exception class and its API have changed.

```php
// Before
use MercadoPagoException; // global namespace

try {
    $result = $mp->get_payment($id);
} catch (MercadoPagoException $e) {
    echo $e->getCode();    // HTTP status
    echo $e->getMessage(); // raw string, e.g. "Some message - 123: description"
}

// After
use MercadoPago\Exceptions\MPApiException;

try {
    $payment = $client->get($id);
} catch (MPApiException $e) {
    // API errors (4xx / 5xx after all retries)
    echo $e->getStatusCode();                    // int HTTP status
    echo $e->getApiResponse()->getStatusCode();  // same
    print_r($e->getApiResponse()->getContent()); // decoded JSON array
    // e.g. ["message" => "...", "error" => "not_found", "cause" => [...]]
} catch (\Exception $e) {
    // Transport errors: timeouts, DNS failures, curl errors
    echo $e->getMessage();
}
```

Key differences:
- `MPApiException` gives you the full structured API response — no more string parsing.
- Transport errors (curl, DNS, timeout) are plain `\Exception`, not `MPApiException`.
- 5xx errors are **automatically retried** with exponential backoff before the exception reaches your code.

---

## Step 5 — Per-Request Overrides (New)

You can now override the access token, timeout, or add custom headers on a per-request basis without changing global config.

```php
use MercadoPago\Client\Common\RequestOptions;

$options = new RequestOptions();
$options->setAccessToken("APP_USR-other-token"); // multi-tenant use cases
$options->setConnectionTimeout(5000);
$options->setCustomHeaders(["X-Idempotency-Key: my-unique-key"]);

$payment = $client->create($data, $options);
```

---

## Step 6 — Webhook Signature Validation (New)

The current SDK includes built-in webhook signature validation, which was absent in the deprecated SDK.

```php
use MercadoPago\Webhook\WebhookSignatureValidator;
use MercadoPago\Exceptions\InvalidWebhookSignatureException;

try {
    WebhookSignatureValidator::validate(
        xSignature: $_SERVER['HTTP_X_SIGNATURE'],
        xRequestId: $_SERVER['HTTP_X_REQUEST_ID'],
        dataId:     $_GET['data_id'] ?? null,
        secret:     "YOUR_WEBHOOK_SECRET",
        toleranceSeconds: 300
    );
    // Signature valid — process the event
} catch (InvalidWebhookSignatureException $e) {
    http_response_code(401);
    echo $e->getReason(); // e.g. "SignatureMismatch"
}
```

---

## New Clients Available

The current SDK covers resources that had no dedicated support in the deprecated SDK:

| Client | Resource |
|---|---|
| `OrderClient` | New Orders API |
| `AdvancedPaymentClient` | Marketplace split payments |
| `ChargebackClient` | Dispute records |
| `PreApprovalClient` | Subscriptions |
| `PreApprovalPlanClient` | Subscription plans |
| `MerchantOrderClient` | Merchant orders |
| `PointClient` | POS / in-person payments |
| `OAuthClient` | OAuth flow |
| `CardTokenClient` | Card tokenization |
| `PaymentMethodClient` | Available payment methods |
| `UserClient` | User info |

---

## Quick Reference

| Concern | Deprecated | Current |
|---|---|---|
| Bootstrap | `new MP("TOKEN")` | `MercadoPagoConfig::setAccessToken("TOKEN")` |
| Sandbox | `$mp->sandbox_mode(true)` | `MercadoPagoConfig::setRuntimeEnviroment(MercadoPagoConfig::LOCAL)` |
| Create payment | `$mp->post(["uri"=>"/v1/payments","data"=>$d])` | `(new PaymentClient())->create($d)` |
| Get payment | `$mp->get_payment($id)` | `(new PaymentClient())->get($id)` |
| Cancel payment | `$mp->cancel_payment($id)` | `(new PaymentClient())->cancel($id)` |
| Total refund | `$mp->refund_payment($id)` | `(new PaymentRefundClient())->refundTotal($id)` |
| Partial refund | Not supported | `(new PaymentRefundClient())->refund($id, $amount)` |
| Create preference | `$mp->create_preference($data)` | `(new PreferenceClient())->create($data)` |
| Response type | `array["status"=>200,"response"=>[...]]` | Typed object: `$payment->status` |
| API exception | `catch (MercadoPagoException $e)` | `catch (MPApiException $e)` |
| Error detail | String parse from `$e->getMessage()` | `$e->getApiResponse()->getContent()` |
| Retry on 5xx | None | Automatic exponential backoff |
| Per-request token | Not supported | `RequestOptions::setAccessToken()` |
| Webhook validation | Not supported | `WebhookSignatureValidator::validate()` |
