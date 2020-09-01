[![Actions Status](https://github.com/renekorss/Banklink/workflows/build/badge.svg)](https://github.com/renekorss/Banklink/actions)
[![Coverage Status](https://coveralls.io/repos/renekorss/Banklink/badge.svg?branch=master&service=github)](https://coveralls.io/github/renekorss/Banklink?branch=master)
[![Codacy Badge](https://api.codacy.com/project/badge/Grade/e40d4d283c7e41b2993656fce3645439)](https://www.codacy.com/app/renekorss/Banklink?utm_source=github.com&amp;utm_medium=referral&amp;utm_content=renekorss/Banklink&amp;utm_campaign=Badge_Grade)
[![Latest Stable Version](https://poser.pugx.org/renekorss/banklink/v/stable)](https://packagist.org/packages/renekorss/banklink)
[![Total Downloads](https://poser.pugx.org/renekorss/banklink/downloads)](https://packagist.org/packages/renekorss/banklink)
[![License](https://img.shields.io/badge/license-MIT-blue.svg)](LICENSE)
[![SymfonyInsight](https://insight.symfony.com/projects/4cf7fcfd-17f3-46a0-af94-0742c4332e16/mini.svg)](https://insight.symfony.com/projects/4cf7fcfd-17f3-46a0-af94-0742c4332e16)

# PHP Payment library

> PHP payment library to easily integrate Baltic banklinks, E-commerce gateaway (Estcard, Nets Estonia), Liizi Payment Link and Pocopay.
>
> View API documentation at https://renekorss.github.io/Banklink/

## Install

````bash
composer require renekorss/banklink
````

## Supported providers

Country / Provider| Payment             | Authentication
------------------| ------------------- | ------------------
**Estonia**       |                     |
Danskebank        | :white_check_mark:  | :white_check_mark:
Coop Pank         | :white_check_mark:  | :white_check_mark:
LHV               | :white_check_mark:  | :white_check_mark:
SEB               | :white_check_mark:  | :white_check_mark:
Swedbank          | :white_check_mark:  | :white_check_mark:
Luminor           | :white_check_mark:  | :white_check_mark:
Nordea            | :white_check_mark:  | :white_check_mark:
Pocopay           | :white_check_mark:  | does not apply
Estcard           | :white_check_mark:  | does not apply
Liisi Payment Link| :white_check_mark:  | does not apply
**Lithuania**     |                     |
SEB               | :white_check_mark:  | does not apply
Swedbank          | :white_check_mark:  | does not apply
Luminor           | :white_check_mark:  | does not apply
Šiaulių           | :white_check_mark:  | does not apply
Estcard           | :white_check_mark:  | does not apply

## How to use?

For more information, please visit [Wiki](https://github.com/renekorss/Banklink/wiki). Basic example is below.

> **SECURITY WARNING**
>
> Never keep your private and public keys in publicly accessible folder. Instead place keys **under** root folder (usually `public_html` or `www`).
>
> If you store keys as strings in database, then they should be accessible only over HTTPS protocol.

### Payment

````php
<?php
    require __DIR__ . '/vendor/autoload.php';

    use RKD\Banklink;

    // Init protocol
    $protocol = new Banklink\Protocol\IPizza(
        'uid100010', // seller ID (VK_SND_ID)
        __DIR__ . '/../keys/seb_user_key.pem', // private key
        '', // private key password, leave empty, if not needed
        __DIR__ . '/../keys/seb_bank_cert.pem', // public key
        'http://localhost/banklink/SEB.php' // return url
    );

    // Init banklink
    $seb = new Banklink\EE\SEB($protocol);

    // Set payment data and get payment request object
    // orderId, sum, message, language
    $request = $seb->getPaymentRequest(123453, 150, 'Test makse', 'EST');

    // You can also add custom request data and/or override request data
    // Optional
    $request = $seb->getPaymentRequest(123453, 150, 'Test makse', 'EST', 'EUR', [
        'VK_REF' => 'my_custom_reference_number', // Override reference number
        'INAPP' => true // Pocopay specific example
    ]);
?>

<form method="POST" action="<?php echo $request->getRequestUrl(); ?>">
  <?php echo $request->getRequestInputs(); ?>
  <input type="submit" value="Pay with SEB!" />
</form>

````

### Authentication

````php
<?php
    require __DIR__ . '/vendor/autoload.php';

    use RKD\Banklink;

    // Init protocol
    $protocol = new Banklink\Protocol\IPizza(
        'uid100010', // seller ID (SND ID)
        __DIR__ . '/../keys/seb_user_key.pem', // private key
        '', // private key password, leave empty, if not needed
        __DIR__ . '/../keys/seb_bank_cert.pem', // public key
        'http://localhost/banklink/SEB.php' // return url
    );

    // Init banklink
    $seb = new Banklink\EE\SEB($protocol);

    // Get auth request object
    $request = $seb->getAuthRequest();
?>

<form method="POST" action="<?php echo $request->getRequestUrl(); ?>">
  <?php echo $request->getRequestInputs(); ?>
  <input type="submit" value="Authenticate with SEB!" />
</form>

````

### Response from provider

````php
<?php
    require __DIR__ . '/vendor/autoload.php';

    use RKD\Banklink;

    // Init protocol
    $protocol = new Banklink\Protocol\IPizza(
        'uid100010', // seller ID (SND ID)
        __DIR__ . '/../keys/seb_user_key.pem', // private key
        '', // private key password, leave empty, if not needed
        __DIR__ . '/../keys/seb_bank_cert.pem', // public key
        'http://localhost/banklink/SEB.php' // return url
    );

    // Init banklink
    $seb = new Banklink\EE\SEB($protocol);

    // Get response object
    $response = $seb->handleResponse($_POST);

    // Successful
    if ($response->wasSuccessful()) {
        // Get whole array of response
        $responseData    = $response->getResponseData();

        // User prefered language
        $language        = $response->getLanguage();

        // Only for payment data
        $orderId         = $response->getOrderId();
        $sum             = $response->getSum();
        $currency        = $response->getCurrency();
        $sender          = $response->getSender();
        $transactionId   = $response->getTransactionId();
        $transactionDate = $response->getTransactionDate();
        $message         = $response->getMessage();
        $automatic       = $response->isAutomatic(); // true if response was sent automatically by bank

        // Only for auth data
        $userId          = $response->getUserId(); // Person ID
        $userName        = $response->getUserName(); // Person name
        $country         = $response->getUserCountry(); // Person country
        $authDate        = $response->getAuthDate(); // Authentication response datetime

        // Method used for authentication
        // Possible values: ID Card, Mobile ID, One-off code card, PIN-calculator, Code card or unknown
        $authMethod      = $response->getAuthMethod();

    // Failed
    } else {
        // Payment data
        $orderId         = $response->getOrderId(); // Order id to cancel order etc.
    }
?>

````

## Tasks

- `composer build` - build by running tests and all code checks
- `composer test` - run tests
- `composer format` - format code against standards
- `composer docs` - build API documentation
- `composer phpmd` - run PHP Mess Detector
- `composer phpcs` - run PHP CodeSniffer

## License

Licensed under [MIT](LICENSE)
