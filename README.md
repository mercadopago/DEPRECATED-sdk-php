# Mercadopago SDK module for Payments integration

* [Usage](#usage)
* [Using Mercadopago Checkout](#checkout)
* [Using Mercadopago Payment collection](#payments)

<a name="usage"></a>
### Usage:

1. Copy lib/mercadopago.php to your project desired folder.
2. Copy lib/cacert.pem to the same folder (for SSL access to MercadoPago APIs).

* Get your credentials at [Mercadopago Developers Site](https://developers.mercadopago.com/beta/api-de-checkout#get-credentials).
* Replace your CLIENT_ID and CLIENT_SECRET.

```php
require_once "mercadopago.php";

$mp = new MP ("CLIENT_ID", "CLIENT_SECRET");
```

<a name="checkout"></a>
### Using Mercadopago Checkout

Get an existent Checkout preference:

```php
$preferenceResult = $mp->get_preference("PREFERENCE_ID");

print_r ($preferenceResult);
```

Create a Checkout preference:

```php
$preference = array (
	"items" => array (
		array (
			"title" => "Test",
			"quantity" => 1,
			"currency_id" => "USD",
			"unit_price" => 10.4
		)
	)
);

$preferenceResult = $mp->create_preference($preference);

print_r ($preferenceResult);
```

Update an existent Checkout preference:

```php
$preference = array (
	"items" => array (
		array (
			"title" => "Test Modified",
			"quantity" => 1,
			"currency_id" => "USD",
			"unit_price" => 20.4
		)
	)
);

$preferenceResult = $mp->update_preference("PREFERENCE_ID", $preference);

print_r ($preferenceResult);
```

<a name="payments"></a>
### Using Mercadopago Payment

Searching:

```php
$filters = array (
        "id": null,
        "site_id": null,
        "external_reference": null
    };

$searchResult = $mp->search_payment ($filters);

print_r ($searchResult);
```

Receiving IPN notification:

```php
require_once "mercadopago.php";

header("Content-type: text/plain");

$mp = new MP ("CLIENT_ID", "CLIENT_SECRET");
$paymentInfo = $mp->get_payment_info ($_GET["id"]);

header ("", true, $paymentInfo["status"]);

print_r ($paymentInfo);
```

Cancel (only for pending payments):

```php
$result = $mp->cancel_payment($_GET["ID"]);

// Show result
print_r ($result);
```

Refund (only for accredited payments):

```php
$result = $mp->refund_payment($_GET["ID"]);

// Show result
print_r ($result);
```