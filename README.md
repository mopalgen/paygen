# PayGen Payment Gateway Integration in PHP

## Installation

``` 
composer require mopalgen/paygen
```

Then add the following scripts before closing `<head>` tag

```html
<script src="https://paygen.transactiongateway.com/js/v1/Gateway.js"></script>
<script src="https://paygen.transactiongateway.com/token/Collect.js"
        data-tokenization-key="PUBLIC_SECURITY_TOKEN_HERE"
        data-variant="inline"
></script>
```

Then add the following before closing `<body>` tag

**For 3D Secure Authentication**
```html
<script>
    const gateway = Gateway.create('CHECKOUT_PUBLIC_SECURITY_TOKEN_HERE');
    const threeDSecure = gateway.get3DSecure();
    
    // Create 3DS UI instance
    const threeDSecureUI = threeDSecure.createUI({
        containerId: 'threeds-container'
    });

    // Start authentication when needed
    function start3DSAuth(cardData) {
        threeDSecureUI.start({
            amount: 'AMOUNT',  // e.g. '10.00'
            currency: 'CURRENCY', // e.g. 'USD'
            card: cardData
        });
    }

    // Handle 3DS authentication result
    threeDSecureUI.on('authenticated', (result) => {
        const threeDSData = {
            cardholder_auth: result.status === 'Y' ? 'verified' : 'attempted',
            cavv: result.cavv,
            xid: result.xid,
            three_ds_version: result.version,
            directory_server_id: result.dsTransId
        };
        
        // Add 3DS data to your form
        let checkout = document.getElementById("checkout-form");
        Object.entries(threeDSData).forEach(([key, value]) => {
            let input = document.createElement("input");
            input.type = "hidden";
            input.name = "three_ds_data[" + key + "]";
            input.value = value;
            checkout.appendChild(input);
        });
    });

    // Handle any errors
    threeDSecureUI.on('error', (error) => {
        console.error('3DS Error:', error);
    });
</script>
```

**For Kount Fraud Detection**

```html
<script>
    const gateway = Gateway.create('CHECKOUT_PUBLIC_SECURITY_TOKEN_HERE');
    const kount = gateway.getKount();

    kount.createSession().then((response) => {
        const transactionSessionId = response;

        let checkout = document.getElementById("checkout-form");
        let transactionSessionIdInput = document.createElement("input");
        transactionSessionIdInput.type = "hidden";
        transactionSessionIdInput.name = "transaction_session_id";
        transactionSessionIdInput.value = transactionSessionId;
        checkout.appendChild(transactionSessionIdInput);
    });

    // Listen for any errors that might occur
    gateway.on('error', function (e) {
        console.error(e);
    });
</script>
```

**For PayGen CollectJS Configuration**

```html
<script>
    document.addEventListener('DOMContentLoaded', function () {
        CollectJS.configure({
            'theme': 'material',
            'customCss': {
                'border-width' : '1px',
                'border-radius': '0.5rem',
                'border-color': '#d1d5db',
                'padding': '10px 40px 10px 10px',
                'height': '44px'
            },
            'invalidCss': {
                'border-color': '#f87171',
                'color': '#f87171',
            },
            'validCss': {
                'border-color': '#22c55e',
            },
            'focusCss': {
                'border-color': '#1d4ed8',
                'border-width': '2px'
            },
            'paymentSelector' : '#card-pay-button',
            'fields': {
                'ccnumber': {
                    'placeholder': '0000 0000 0000 0000',
                },
                'cvv': {
                    'placeholder': '123',
                },
                'ccexp': {
                    'placeholder': '00 / 00',
                },
                'googlePay': {
                    'selector': '#google-pay-button',
                    'buttonType': 'pay',
                    'emailRequired': true,
                    'shippingAddressRequired': true,
                    'shippingAddressParameters': {
                        'phoneNumberRequired': true
                    },
                    'billingAddressRequired': true,
                    'billingAddressParameters': {
                        'phoneNumberRequired': true,
                        'format': 'MIN'
                    },
                },
                'applePay' : {
                    'selector': '#apple-pay-button',
                    'type': 'buy',
                },
            },
            "price": "PAY_AMOUNT",
            "currency":"PAY_CURRENCY",
            "country": "PAY_COUNTRY",
            'callback' : function(response) {
                let checkout = document.getElementById("checkout-form");
                let payment_token = document.createElement("input");
                payment_token.type = "hidden";
                payment_token.name = "payment_token";
                payment_token.value = response.token;
                checkout.appendChild(payment_token);
                checkout.submit();
            }
        });
    });
</script>
```

***Important Note :*** 
- 'PAY_AMOUNT' should be in format of 10.00
- 'PAY_CURRENCY' should be in format of 'USD'
- 'PAY_COUNTRY' should be in format of 'US'
- No input fields needs to be added for card number, expiry date and cvv number. They are dynamically injected and validation is added. For adding card input fields :
    - Add empty div with id ***'ccnumber'*** for card number
    - Add empty div with id ***'ccexp'*** for expiry date
    - Add empty div with id ***'cvv'*** for CVV code
    - For Submit Button, make sure to have id of ***'card-pay-button'***
- For Google Pay, add empty div with id ***'google-pay-button'***
- For Apple Pay, add empty div with id ***'apple-pay-button'***
- Please note, we are not capturing or storing any card credentials.

## Integration Guide

### Pay using credit card with 3DS
```php
// Create a new customer
$customer = new Mopalgen\Paygen\Customer();
$customer->setBillingAddress(
    $_POST['firstname'],
    $_POST['lastname'],
    $_POST['address1'],
    $_POST['address2'],
    $_POST['city'],
    $_POST['state'],
    $_POST['country'],
    $_POST['zip']
);
$customer->setShippingAddress(
    $_POST['shipping_firstname'],
    $_POST['shipping_lastname'],
    $_POST['shipping_address1'],
    $_POST['shipping_address2'],
    $_POST['shipping_city'],
    $_POST['shipping_state'],
    $_POST['shipping_country'],
    $_POST['shipping_zip']
);
$customer->setIPAddress($_SERVER['REMOTE_ADDR']);

// Prepare 3DS data from the authentication
$three_ds_data = isset($_POST['three_ds_data']) ? $_POST['three_ds_data'] : null;

// Initiate Payment Processor
$paymentProcessor = new Mopalgen\Paygen\Init(PRIVATE_SECURITY_TOKEN_HERE, $customer);

if(ENABLE_KOUNT_FRAUD_DETECTION) {
    $response = $paymentProcessor->createOrder(
        $order_details, 
        $_POST['payment_method'], 
        $_POST['payment_token'], 
        $_POST['transaction_session_id'],
        $three_ds_data
    );
} else {
    $response = $paymentProcessor->createOrder(
        $order_details, 
        $_POST['payment_method'], 
        $_POST['payment_token'],
        "",
        $three_ds_data
    );
}
```

Handle $response and save related data accordingly.

Please make sure the save the following variables from response :
- responsetext
- response_code
- authcode
- transactionid
- avsresponse
- cvvresponse

'$order_details' should be in below format :

```
$order_details = [
    'order_id' => '1',
    'billing_country' => 'US',
    'billing_currency' => 'USD',
    'items' => [
        [
            'name' => 'Product #1',
            'price' => '10.00',
            'qty' => 1,
            'subtotal' => '10.00',
        ],
        [
            'name' => 'Product #2',
            'price' => '10.00',
            'qty' => 2,
            'subtotal' => '20.00',
        ]
    ],
    'sub_total' => '30.00',
    'tax' => '6.00',
    'shipping_rate' => '5.00',
    'total' => '41.00'
];
```

### Save credit card to customer vault
```php
$customer = new Mopalgen\Paygen\Customer();
$response = $customer->addCardToCustomerVault(SECURITY_KEY, $_POST['payment_token']);
```

Handle $response and save related data accordingly.

Please make sure the save the following variables from response :
- responsetext
- response_code
- customer_vault_id

Please store customer_vault_id to use the stored card in future.

Also, please note we are using payment_token instead card credentials for security purpose.

### Pay using saved credit card with 3DS
```php
// Create a new customer
$customer = new Mopalgen\Paygen\Customer();
$customer->setBillingAddress(
    $_POST['firstname'],
    $_POST['lastname'],
    $_POST['address1'],
    $_POST['address2'],
    $_POST['city'],
    $_POST['state'],
    $_POST['country'],
    $_POST['zip']
);
$customer->setShippingAddress(
    $_POST['shipping_firstname'],
    $_POST['shipping_lastname'],
    $_POST['shipping_address1'],
    $_POST['shipping_address2'],
    $_POST['shipping_city'],
    $_POST['shipping_state'],
    $_POST['shipping_country'],
    $_POST['shipping_zip']
);
$customer->setIPAddress($_SERVER['REMOTE_ADDR']);

// Prepare 3DS data from the authentication
$three_ds_data = isset($_POST['three_ds_data']) ? $_POST['three_ds_data'] : null;

// Initiate Payment Processor
$paymentProcessor = new Mopalgen\Paygen\Init(SECURITY_KEY, $customer);

if(ENABLE_KOUNT_FRAUD_DETECTION) {
    $response = $paymentProcessor->createOrderUsingSavedCard(
        $order_details, 
        $_POST['payment_method'], 
        $_POST['customer_vault_id'], 
        $_POST['transaction_session_id'],
        $three_ds_data
    );
} else {
    $response = $paymentProcessor->createOrderUsingSavedCard(
        $order_details, 
        $_POST['payment_method'], 
        $_POST['customer_vault_id'],
        "",
        $three_ds_data
    );
}
```
Here instead of 'payment_token', we are using 'customer_vault_id' to use the stored card for payment.

Please make sure the save the following variables from response :
- responsetext
- response_code
- authcode
- transactionid
- avsresponse
- cvvresponse

### Update saved card from customer vault
```php
$customer = new Mopalgen\Paygen\Customer();
$response = $customer->updateCardFromCustomerVault(SECURITY_KEY, $_POST['customer_vault_id'], $_POST['payment_token']);
```

Handle $response and save related data accordingly.

### Delete saved card from customer vault
```php
$customer = new Mopalgen\Paygen\Customer();
$response = $customer->deleteCardFromCustomerVault(SECURITY_KEY, $_POST['customer_vault_id']);
```

Handle $response and save related data accordingly.

### Response Codes

- 100 : Transaction was approved.
- 200 : Transaction was declined by processor.
- 201 : Do not honor.
- 202 : Insufficient funds.
- 203 : Over limit.
- 204 : Transaction not allowed.
- 220 : Incorrect payment information.
- 221 : No such card issuer.
- 222 : No card number on file with issuer.
- 223 : Expired card.
- 224 : Invalid expiration date.
- 225 : Invalid card security code.
- 226 : Invalid PIN.
- 240 : Call issuer for further information.
- 250 : The customer's card has been reported as lost or stolen by the cardholder 
- 251 : Lost card.
- 252 : Stolen card.
- 253 : Fraudulent card.
- 260 : Declined with further instructions available.
- 261 : Declined-Stop all recurring payments.
- 262 : Declined-Stop this recurring program.
- 263 : Declined-Update cardholder data available.
- 264 : Declined-Retry in a few days.
- 300 : Transaction was rejected by gateway.
- 400 : Transaction error returned by processor.
- 410 : Invalid merchant configuration.
- 411 : Merchant account is inactive.
- 420 : Communication error.
- 421 : Communication error with issuer.
- 430 : Duplicate transaction at processor.
- 440 : Processor format error.
- 441 : Invalid transaction information.
- 460 : Processor feature not available.
- 461 : Unsupported card type.

### CVV Response Codes
- M	: CVV2/CVC2 match
- N	: CVV2/CVC2 no match
- P	: Not processed
- S	: Merchant has indicated that CVV2/CVC2 is not present on card
- U	: Issuer is not certified and/or has not provided Visa encryption keys

### AVS Response Codes
- X : Exact match, 9-character numeric ZIP
- Y : Exact match, 5-character numeric ZIP
- D : Exact match, 5-character numeric ZIP
- M : Exact match, 5-character numeric ZIP
- 2 : Exact match, 5-character numeric ZIP, customer name
- 6 : Exact match, 5-character numeric ZIP, customer name
- A : Address match only
- B : Address match only
- 3 : Address, customer name match only
- 7 : Address, customer name match only
- W : 9-character numeric ZIP match only
- Z : 5-character ZIP match only
- P : 5-character ZIP match only
- L : 5-character ZIP match only
- 1 : 5-character ZIP, customer name match only
- 5 : 5-character ZIP, customer name match only
- N : No address or ZIP match only
- C : No address or ZIP match only
- 4 : No address or ZIP or customer name match only
- 8 : No address or ZIP or customer name match only
- U : Address unavailable
- G : Non-U.S. issuer does not participate
- I : Non-U.S. issuer does not participate
- R : Issuer system unavailable
- E : Not a mail/phone order
- S : Service not supported
- 0 : AVS not available
- O : AVS not available
- B : AVS not available

### Demo Project

You can find a basic implimentation of the payment gateway here: https://github.com/mopalgen/paygen-demo

### Add card to customer vault with 3DS
```php
$customer = new Mopalgen\Paygen\Customer();

// Prepare 3DS data from the authentication
$three_ds_data = isset($_POST['three_ds_data']) ? $_POST['three_ds_data'] : null;

$response = $customer->addCardToCustomerVault(
    SECURITY_KEY, 
    $_POST['payment_token'],
    $three_ds_data
);
```

### Update saved card in customer vault with 3DS
```php
$customer = new Mopalgen\Paygen\Customer();

// Prepare 3DS data from the authentication
$three_ds_data = isset($_POST['three_ds_data']) ? $_POST['three_ds_data'] : null;

$response = $customer->updateCardFromCustomerVault(
    SECURITY_KEY,
    $_POST['customer_vault_id'], 
    $_POST['payment_token'],
    $three_ds_data
);
```
