[![Build Status](https://travis-ci.org/renekorss/Banklink.svg?branch=master)](https://travis-ci.org/renekorss/Banklink)
[![Coverage Status](https://coveralls.io/repos/renekorss/Banklink/badge.svg?branch=master&service=github)](https://coveralls.io/github/renekorss/Banklink?branch=master)
[![Test Status](https://php-eye.com/badge/renekorss/Banklink/tested.svg)](https://php-eye.com/package/renekorss/banklink)
[![Codacy Badge](https://api.codacy.com/project/badge/Grade/e40d4d283c7e41b2993656fce3645439)](https://www.codacy.com/app/renekorss/Banklink?utm_source=github.com&amp;utm_medium=referral&amp;utm_content=renekorss/Banklink&amp;utm_campaign=Badge_Grade)
[![Latest Stable Version](https://poser.pugx.org/renekorss/banklink/v/stable)](https://packagist.org/packages/renekorss/banklink)
[![Total Downloads](https://poser.pugx.org/renekorss/banklink/downloads)](https://packagist.org/packages/renekorss/banklink)
[![License](http://img.shields.io/badge/license-MIT-blue.svg)](LICENSE)
[![SensioLabsInsight](https://insight.sensiolabs.com/projects/4cf7fcfd-17f3-46a0-af94-0742c4332e16/mini.png)](https://insight.sensiolabs.com/projects/4cf7fcfd-17f3-46a0-af94-0742c4332e16)

# PHP Payment library

> PHP payment library to easily integrate Baltic banklinks, E-commerce gateaway (Estcard, Nets Estonia) and Liizi Payment Link.

> View API documentation at http://renekorss.github.io/Banklink/

## Composer

    composer require renekorss/Banklink

## Supported providers

Provider          | Payment             | Authentication    
------------------| ------------------- | ------------------
Danskebank        | :white_check_mark:  | :white_check_mark:
Coop Pank         | :white_check_mark:  | :white_check_mark:
LHV               | :white_check_mark:  | :white_check_mark:
SEB               | :white_check_mark:  | :white_check_mark:
Swedbank          | :white_check_mark:  | :white_check_mark:
Luminor           | :white_check_mark:  | :white_check_mark:
Nordea            | :white_check_mark:  | :white_check_mark:
Estcard           | :white_check_mark:  | does not apply
Liisi Payment Link| :white_check_mark:  | does not apply

## How to use?

> **SECURITY WARNING**

> Never keep your private and public keys in publicly accessible folder. Instead place keys **under** root folder (usually `public_html` or `www`).

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
    $seb = new Banklink\SEB($protocol);

    // Set payment data and get payment request object
    // orderId, sum, message, language
    $request = $seb->getPaymentRequest(123453, 150, 'Test makse', 'EST');
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
    $seb = new Banklink\SEB($protocol);

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
    $seb = new Banklink\SEB($protocol);

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

 - `composer tests` - run tests
 - `composer docs` - build API documentation
 - `composer phpmd` - run PHP Mess Detector
 - `composer phpcs` - run PHP CodeSniffer

## Testing your banklink

You can test your banklink with <a href="http://pangalink.net/" target="_blank">pangalink.net</a> application (Windows, Mac, Linux).

## License

Licensed under [MIT](LICENSE)
