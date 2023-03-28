<?php

namespace RKD\Banklink\Test\EE;

use UnexpectedValueException;
use PHPUnit\Framework\TestCase;
use RKD\Banklink\Response\PaymentResponse;
use RKD\Banklink\Protocol\Helper\ProtocolHelper;

/**
 * Test suite for Coop Pank banklink.
 *
 * @author  Rene Korss <rene.korss@gmail.com>
 *
 * @SuppressWarnings(PHPMD.TooManyFields)
 * @SuppressWarnings(PHPMD.TooManyPublicMethods)
 */
class CoopPankTest extends TestCase
{
    protected $bankClass = "RKD\Banklink\EE\CoopPank";
    protected $protocolClass = "RKD\Banklink\Protocol\IPizza";

    protected $requestUrl = [
        'payment' => 'https://i.cooppank.ee/pay',
        'auth' => 'https://i.cooppank.ee/auth'
    ];
    protected $testRequestUrl = [
        'payment' => 'https://secure.cooppank.ee/pay',
        'auth' => 'https://secure.cooppank.ee/auth'
    ];

    protected $bank;

    protected $protocol;

    protected $sellerId;
    protected $sellerName;
    protected $sellerAccount;

    protected $senderName;

    protected $orderId;
    protected $amount;
    protected $message;
    protected $language;
    protected $currency;
    protected $timezone;
    protected $datetime;
    protected $expectedData;

    protected $customRequestUrl;

    /**
     * Set test data.
     */
    public function setUp() : void
    {
        $this->sellerId         = 'id2000';
        $this->sellerName       = 'Ülo Pääsuke';
        $this->sellerAccount    = '1010342342354345435';

        $this->senderName       = 'Toomas Jäär';

        $this->orderId          = 100;
        $this->amount           = 10.00;
        $this->message          = 'First payment';
        $this->language         = 'EST';
        $this->currency         = 'EUR';
        $this->timezone         = 'Europe/Tallinn';

        // From ENV variable
        $this->datetime         = getenv('TEST_DATETIME');

        $this->customRequestUrl = 'http://example.com';

        $this->expectedData = [
            'VK_SERVICE'  => '1012',
            'VK_VERSION'  => '009',
            'VK_SND_ID'   => $this->sellerId,
            'VK_STAMP'    => $this->orderId,
            'VK_AMOUNT'   => $this->amount,
            'VK_CURR'     => $this->currency,
            'VK_REF'      => ProtocolHelper::calculateReference($this->orderId),
            'VK_MSG'      => $this->message,
            'VK_RETURN'   => $this->customRequestUrl,
            'VK_CANCEL'   => $this->customRequestUrl,
            'VK_LANG'     => $this->language,
            'VK_MAC'      => 'b675KD4mg4V8t22IriOGn/wuur3O//SmzPy/yr7/MgxHojBeqIW1rcQIT8pcEklsSJ+HfUK7PZJZlcUW1S086l64JdGyBBAePIFIYPHbrefkOnhmCR3hkOqtkKgXSAr1eKigOygV48l6gkJ2rKZmT2Koc9+jLvEC7MOocaWotqNcyIap6cgYKlIDXaEEsEEbJz4pRN0qRjFYxGDH40qYI4nuO2bajC4Hx5e9MCRDlTZY5A5PJRuCEfdO5i5jBgPDecjtdT7NdTHnnW3LBT6SghToVXlAhRniI843VnfdeFy5pKcUuMGswUBnmv0pkVZJMReyUf51su2iC0aRo1JvgD6ULRDX9bqZ8dtOeh4EaYVWBeq+wPPwvuSPA0xDYFEE5rjhrqHmQT02LGv7274aTrLSS8MgDw9sAjpDghgS6VQdWujjy7KeeKIrjbTFpIB/GDKJ4hSIFS/sPew2xF3VGtGqfaGe2mCzF3KiegYwwAjjaiOuU8qgCalmf2In1euj',
            'VK_DATETIME' => $this->datetime,
            'VK_ENCODING' => 'UTF-8',
        ];

        // Set up banklink
        $this->setUpBanklink();
    }

    /**
     * Set up banklink object
     */

    protected function setUpBanklink()
    {
        $this->protocol = new $this->protocolClass(
            $this->sellerId,
            __DIR__.'/../keys/IPizza/private_key_sha512.pem',
            '',
            __DIR__.'/../keys/IPizza/public_key_sha512.pem',
            $this->customRequestUrl,
            null,
            null,
            '009'
        );

        // Use mb_strlen
        $this->protocol->useMbStrlen(true);
        $this->protocol->setAlgorithm(OPENSSL_ALGO_SHA512);
        $this->bank = new $this->bankClass($this->protocol);
    }

    /**
     * Set up banklink object with seller data
     */

    protected function setUpBanklinkWithSeller()
    {
        $this->protocol = new $this->protocolClass(
            $this->sellerId,
            __DIR__.'/../keys/IPizza/private_key.pem',
            '',
            __DIR__.'/../keys/IPizza/public_key.pem',
            $this->customRequestUrl,
            $this->sellerName,
            $this->sellerAccount,
            '009'
        );

        // Use mb_strlen
        $this->protocol->useMbStrlen(true);
        $this->protocol->setAlgorithm(OPENSSL_ALGO_SHA512);
        $this->bank = new $this->bankClass($this->protocol);
    }

    /**
     * Test for correctly generated request data for service 1012.
     */
    public function testGetPaymentRequestService1012()
    {

        // Test service 1012
        $request = $this->bank->getPaymentRequest($this->orderId, $this->amount, $this->message, $this->language, $this->currency, [], $this->timezone);

        // Instance of PaymentRequest and data is same
        $this->assertInstanceOf('RKD\Banklink\Request\PaymentRequest', $request);
        $this->assertEquals($this->expectedData, $request->getRequestData());

        // Production env url
        $this->assertEquals($this->requestUrl['payment'], $request->getRequestUrl());
    }

    /**
     * Test for correctly generated request data for service 1011
     * Test debug url.
     */
    public function testGetPaymentRequestService1011()
    {
        $this->setUpBanklinkWithSeller();

        // Debug mode
        $this->bank->debugMode();

        // New expected values
        $this->expectedData['VK_SERVICE']  = '1011';
        $this->expectedData['VK_ACC']      = $this->sellerAccount;
        $this->expectedData['VK_NAME']     = $this->sellerName;
        $this->expectedData['VK_MAC']      = 'Eyv1o/2a84QB662G1Z0HAHEAFgH26SG9M8SDEcdD+CHZm78+6j7EcxBrbiEtTwDcnVTYic7pgfS18ZZDk8Qiq/8a6apdfZabLKVG/xnVIiwLIpAG5KS9GZ9+rqIOGlX/4ts9jXLiqXRrSI1UuBAKGlYsLU7eI/dl5utNqzl0VVI=';
        $this->expectedData['VK_DATETIME'] = $this->datetime;

        $request = $this->bank->getPaymentRequest($this->orderId, $this->amount, $this->message, $this->language, $this->currency, [], $this->timezone);

        // Instance of PaymentRequest and data is same
        $this->assertInstanceOf('RKD\Banklink\Request\PaymentRequest', $request);
        $this->assertEquals($this->expectedData, $request->getRequestData());

        // Test env url
        $this->assertEquals($this->testRequestUrl['payment'], $request->getRequestUrl());

        // Get HTML
        $this->assertStringContainsStringIgnoringCase('<input type="hidden"', $request->getRequestInputs());
    }

    /**
     * Test successful payment response.
     */
    public function testHandlePaymentResponseSuccess()
    {
        $responseData = [
            'VK_SERVICE'    => '1111',
            'VK_VERSION'    => '009',
            'VK_SND_ID'     => $this->senderName,
            'VK_REC_ID'     => $this->sellerId,
            'VK_STAMP'      => $this->orderId,
            'VK_T_NO'       => 100,
            'VK_AMOUNT'     => $this->amount,
            'VK_CURR'       => $this->currency,
            'VK_REC_ACC'    => $this->sellerAccount,
            'VK_REC_NAME'   => $this->sellerName,
            'VK_SND_ACC'    => '101032423434543',
            'VK_SND_NAME'   => 'Mart Mets',
            'VK_REF'        => $this->orderId,
            'VK_MSG'        => $this->message,
            // To generate valid MAC, see comment below
            'VK_MAC'        => 'q4tw9YJ3iNk9O9d+0a17PGwvkO64FhfZoELv3ZaCpyStiKvMFKYv/PMlFiRAPv65JGf2CLk8lrrpXiTfQ6ESnNosl076lyWyrAvPdcl69Dq1SAyV0fCYs8yh+3SO3SxT3uFgBs5XVcdmB8VShpHBW0fVpmjwlk9yDvuLesnP0ztKkFN0hta1+w2qHprgdIzHt5kTjXonUtjw8KAwK7PFu2LFibXtF3AzRr3R5x2UPEQelBGH+PohDbMmj2GtszNtdOUHEigbep1+MV/VjmsaineULx5naA1HPdcm7KJgNVG4K6FA32B2dQCM0PlvrTMGz9BgshjI2KtKPWoo240A9YY0YemcGGHtNthrjtjDq3agTMW4tD66XagCFX8iYUqtgz15Q+n8f9DHaz68ywvvXjkd3xQGDes6o488JcOp4TDk6B5v3ZVfmhPG9yWUwiUpupiROTmrlRf52YzHBf2KSsmL5QiKYjXHeQNl4Sm4Y/kZuoKkXY6sVEQIu1SCJORc',
            'VK_T_DATETIME' => $this->datetime,
            'VK_ENCODING'   => 'UTF-8',
            'VK_AUTO'       => 'Y'
        ];

        // To generate VK_MAC for testing, use:
        // $this->protocol->getSignature($responseData, 'UTF-8')
        // And take VK_MAC from returned array

        $response = $this->bank->handleResponse($responseData);

        $this->assertInstanceOf('RKD\Banklink\Response\PaymentResponse', $response);
        $this->assertSame(PaymentResponse::STATUS_SUCCESS, $response->getStatus());

        $this->assertEquals($this->sellerName, $response->getReceiver()->name);
        $this->assertEquals($this->sellerAccount, $response->getReceiver()->account);

        $this->assertEquals(getenv('TEST_DATETIME'), $response->getTransactionDate());

        // This is valid response
        $this->assertTrue($response->wasSuccessful());

        // Response was sent automatically by bank
        $this->assertTrue($response->isAutomatic());
    }

    /**
     * Test failed payment response.
     */
    public function testHandlePaymentResponseError()
    {
        $responseData = [
            'VK_SERVICE'  => '1911',
            'VK_VERSION'  => '009',
            'VK_SND_ID'   => $this->senderName,
            'VK_REC_ID'   => $this->sellerId,
            'VK_STAMP'    => $this->orderId,
            'VK_REF'      => $this->orderId,
            'VK_MSG'      => $this->message,
            'VK_MAC'      => 'o4rju0oEwITuIheUdtDjp2njKhBzvQv8RjKg+rdCB+fwGiUS8zpXzr0I+wj0vl13h+ACGAR1LO9gR2+IG1yq+AJdQdVszJIbeA1jcg1GFtl1xyLN8LXYfubHHUB/7EWwiEGZKcHrNp3pAsADlLwySQLRWatheMLPqRRk2FX96Ko=',
            'VK_DATETIME' => $this->datetime,
            'VK_AUTO'     => 'N'
        ];

        $response = $this->bank->handleResponse($responseData);

        $this->assertInstanceOf('RKD\Banklink\Response\PaymentResponse', $response);
        $this->assertSame(PaymentResponse::STATUS_ERROR, $response->getStatus());

        // This is not valid response, so validation should fail
        $this->assertFalse($response->wasSuccessful());

        // User pressed "Back to merchant" button
        $this->assertFalse($response->isAutomatic());
    }

    /**
     * Test authentication request data
     * Test service 4011.
     */
    public function testGetAuthRequest4011()
    {
        $expectedData = [
            'VK_SERVICE'  => '4011',
            'VK_VERSION'  => '009',
            'VK_SND_ID'   => 'id2000',
            'VK_RETURN'   => 'http://example.com',
            'VK_DATETIME' => '2015-09-29T15:00:00+0300',
            'VK_RID'      => '',
            'VK_LANG'     => 'EST',
            'VK_REPLY'    => '3012',
            'VK_ENCODING' => 'UTF-8',
            'VK_MAC'      => 'IxjE/6eAn72WvbU7HEtoEVgaldBmE6Nk8JjcXGx4Tqd03WLtHk/QXcMq7JiHuciL7xzMg/SXUXfkpl16MX6y3uP3QheGaNcsjClBkdVih1wD1QARVVfUhOxag9AWewu/kWEOlmelgoxb2hsZxkXC7THCwh29NJfxz07opCNOLrRk5Vcuovru4cIs4xF0HS9DHB2sWVmHGe/rsO7BGnE3NImMNtUWpduGfs05vwDst/3lF6p27y+lSW8tQr/cNVBSCCIHz6ET7DGcvEv/qHJKLSUPZq9LKWm7ew7A975np0yyI3nPBeBQxbLyslimACb4UdnCg3b1v8moQdODrkxAlViHeI8FnLui7PvrIr6uK/mlKplRk//ceynoJkAsp5pkK6j++MhwskwmBgyNSaStCJ2Bvr+QUaD8BnkHvtUeRyE4EE/aRCK27hvZV1pBDUzbdF3lUhTBXMDFHtV48LWyrQ+NJXb0Ho/w3SwRK9I8R2ydeE3vb781kIJVGBXUcO7v',
        ];

        $request = $this->bank->getAuthRequest();

        $this->assertInstanceOf('RKD\Banklink\Request\AuthRequest', $request);
        $this->assertEquals($expectedData, $request->getRequestData());

        // Test env url
        $this->assertEquals($this->requestUrl['auth'], $request->getRequestUrl());

        // Get HTML
        $this->assertStringContainsStringIgnoringCase('<input type="hidden"', $request->getRequestInputs());
    }

    /**
     * Test authentication request data
     * Test service 4012.
     */
    public function testGetAuthRequest4012()
    {
        $expectedData = [
            'VK_SERVICE'  => '4012',
            'VK_VERSION'  => '009',
            'VK_SND_ID'   => 'id2000',
            'VK_REC_ID'   => 'bank-id',
            'VK_NONCE'    => 'random-nonce',
            'VK_RETURN'   => 'http://example.com',
            'VK_DATETIME' => $this->datetime,
            'VK_RID'      => 'random-rid',
            'VK_LANG'     => 'EST',
            'VK_ENCODING' => 'UTF-8',
            'VK_MAC'      => 'uOKvx+r3k/6DjARvpT0Zz6tZHyo6XA6/iXww596g5gAIDTwoel2Rh2NNC2GRtGyXkgOlyhsCs+xaSS8JH4MN1vgDWAAYnArnv6KJBR0TCvsyE8K2nYb1hmc1F8ITEJXFw6v5by0YDLFkhUute1emcDfH93ZEuqithf0zSCUIeJHz2ffIyaqmhpeN4YJHCxVro+KQjHbrY0nBpP2dcJNe42Yvb/ekAjMfh6qoPXby4oOmxzlBckRgbL0w4eOFn0J5nv7gQ7QlQ5aq7ayqC4G/MEo/03EVlfdlC8x6R4hYZnHdv/esqg7Px8adLkCrD4E2WJB8ZrpEhSHqrDyjQnmn7KymdkhIX1v2Oofw1S1iHJjtMQvDsY2VGlY2vmfbH/niWIJSMWr33TTM4qIu/aYu4P5t0kGBaPV+a8dPPC39Ie2ROnkuUdtlF2ClZC5yYoEmFTe+2um8drAs8LMQE76nFX+qxOLHmaaoILhC1OMnLHizQaW0iyRTWydyopD1KLSz',
        ];

        $request = $this->bank->getAuthRequest('bank-id', 'random-nonce', 'random-rid');

        $this->assertInstanceOf('RKD\Banklink\Request\AuthRequest', $request);
        $this->assertEquals($expectedData, $request->getRequestData());

        // Test env url
        $this->assertEquals($this->requestUrl['auth'], $request->getRequestUrl());

        // Get HTML
        $this->assertStringContainsStringIgnoringCase('<input type="hidden"', $request->getRequestInputs());

        // Get same data again, already exists
        $request = $this->bank->getAuthRequest('bank-id', 'random-nonce', 'random-rid');

        $this->assertInstanceOf('RKD\Banklink\Request\AuthRequest', $request);
        $this->assertEquals($expectedData, $request->getRequestData());
    }

    /**
     * Test custom request url.
     */
    public function testCustomRequestUrl()
    {
        $this->bank = new $this->bankClass($this->protocol);
        $this->bank->setRequestUrl('https://google.com');

        $request = $this->bank->getPaymentRequest($this->orderId, $this->amount, $this->message, $this->language, $this->currency, [], $this->timezone);

        // Get same data again, already exists
        $request = $this->bank->getPaymentRequest($this->orderId, $this->amount, $this->message, $this->language, $this->currency, [], $this->timezone);

        // Custom url
        $this->assertSame('https://google.com', $request->getRequestUrl());
    }

    /**
     * Test custom request data
     */
    public function testCustomRequestData()
    {
        $this->bank = new $this->bankClass($this->protocol);

        $customData = [
            'INAPP' => 1, // new data
            'VK_REF' => 'mycustomref', // override data
            'VK_MAC' => 'Ooe2Ldu+05JI9sjGyTdKydkq3IzWNZ3ym2nc2lEDbbDWVAuZ9y7OrdUQhP+CmvaMUKLIimQ2+VBgUMYWU12z9Qrp/JMPXW4V37TgLiS7pkXK3VQItg9VrPvV0IFs1mrjxGE2b2Vf1DTXEJwTH+F2vVlwSOBm0TXmLEGCa9EknvbCZKHrL1SEx5T0Ck6AcW8S2iN4Ld16xVeXE1/76gEXTxM1ooMyjSEwTCa7Y+TDIFr+QeicchA8KIfrcylrFk1dy3fM0FihCvw0dDBJiNWdWE9fWmBRI7C9hfd7IWawKM0O5U1CG1w1cPjbvPxz9elRTlQzjAPEyqLJSoq6TjBPGd5vSvRr/v2ewQ/UnevOllf40X5TIkmvMm3MQ0gu3Qj6FPS0uT0l+Iu08BAkXMhjzHupwGvdHD/coK3ZZtjmVsIV5+UpaCYKUoZxKAWlD1kfBQJKDBSXNel0R7yFEwBobQh76NQfHcNq4aGvqX8cQEsHWOmnUPPoBHlY1jbiYsLV'
        ];

        $this->expectedData = array_merge($this->expectedData, $customData);

        $request = $this->bank->getPaymentRequest($this->orderId, $this->amount, $this->message, $this->language, $this->currency, $customData, $this->timezone);

        // Custom url
        $this->assertEquals($this->expectedData, $request->getRequestData());
    }

    /**
     * Test we can set multiple request urls
     */
    public function testMultipleRequestUrls()
    {
        $this->bank = new $this->bankClass($this->protocol);
        $this->bank->setRequestUrl([
            'auth' => 'https://custom.com/auth',
            'payment' => 'https://custom.com/pay'
        ]);

        // Custom url set
        $this->assertSame('https://custom.com/auth', $this->bank->getRequestUrlFor('auth'));
        $this->assertSame('https://custom.com/pay', $this->bank->getRequestUrlFor('payment'));
    }

    /**
     * Test we can set one request url which is used for payment and authentication
     */
    public function testSingleRequestUrl()
    {
        $this->bank = new $this->bankClass($this->protocol);
        $this->bank->setRequestUrl('https://custom.com/endpoint');

        // Custom url set
        $this->assertSame('https://custom.com/endpoint', $this->bank->getRequestUrlFor('auth'));
        $this->assertSame('https://custom.com/endpoint', $this->bank->getRequestUrlFor('payment'));
    }

    /**
     * getRequestUrlFor should throw an UnexpectedValueException if in wrong type
     */
    public function testRequestUrlWrongType()
    {
        $this->expectException(UnexpectedValueException::class);

        $this->bank = new $this->bankClass($this->protocol);
        $this->bank->setRequestUrl(null);

        $this->assertSame('https://custom.com/endpoint', $this->bank->getRequestUrlFor('payment'));
    }

    /**
     * getRequestUrlFor should throw an UnexpectedValueException if don't have correct type
     */
    public function testNoRequestUrlType()
    {
        $this->expectException(UnexpectedValueException::class);

        $this->bank = new $this->bankClass($this->protocol);
        $this->bank->setRequestUrl([]);

        $this->assertSame('https://custom.com/endpoint', $this->bank->getRequestUrlFor('payment'));
    }
}
