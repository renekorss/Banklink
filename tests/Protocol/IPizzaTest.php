<?php

namespace RKD\Banklink\Test\Protocol;

use InvalidArgumentException;
use PHPUnit\Framework\TestCase;
use RKD\Banklink\Protocol\Helper\ProtocolHelper;
use RKD\Banklink\Protocol\IPizza;
use RKD\Banklink\Request\PaymentRequest;
use RKD\Banklink\Response\AuthResponse;
use RKD\Banklink\Response\PaymentResponse;
use UnexpectedValueException;

/**
 * Test suite for IPizza protocol.
 *
 * @author  Rene Korss <rene.korss@gmail.com>
 */
class IPizzaTest extends TestCase
{
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

    protected $requestUrl;

    /**
     * Set test data.
     */
    public function setUp() : void
    {
        $this->sellerId = 'id2000';
        $this->sellerAccount = '1010342342354345435';
        $this->sellerName = 'Ülo Pääsuke';

        $this->senderName = 'Toomas Jäär';

        $this->orderId = 100;
        $this->amount = 10.00;
        $this->message = 'First payment';
        $this->language = 'EST';
        $this->currency = 'EUR';
        $this->timezone = 'Europe/Tallinn';

        // From ENV variable
        $this->datetime = getenv('TEST_DATETIME');

        $this->requestUrl = 'http://example.com';

        $this->protocol = new IPizza(
            $this->sellerId,
            __DIR__.'/../keys/IPizza/private_key.pem',
            '',
            __DIR__.'/../keys/IPizza/public_key.pem',
            $this->requestUrl
        );

        // Test data
        $this->expectedData = [
            'VK_SERVICE' => '1012',
            'VK_VERSION' => '008',
            'VK_SND_ID' => $this->sellerId,
            'VK_STAMP' => $this->orderId,
            'VK_AMOUNT' => $this->amount,
            'VK_CURR' => $this->currency,
            'VK_REF' => ProtocolHelper::calculateReference($this->orderId),
            'VK_MSG' => $this->message,
            'VK_RETURN' => $this->requestUrl,
            'VK_CANCEL' => $this->requestUrl,
            'VK_LANG' => $this->language,
            'VK_MAC' => 'PmAB256IR1FzTKZHNn5LBPso/KyLAhNcTOMq82lhpYn0mXKYtVtpNkolQxyETnTcIn1TcYOmekJEATe86Bz2MRljEQqllkaIl7bNuLCtuBPtAOYWNLmQHoop+5QSiguJEmEV+JJU3w4BApjWcsHA5HYlYze+3L09UO6na0lB/Zs=',
            'VK_DATETIME' => $this->datetime,
        ];
    }

    /**
     * Test for correctly generated request data for service 1012.
     */
    public function testGetPaymentRequestService1012()
    {

        // Test service 1012
        $requestData = $this->protocol->getPaymentRequest(
            $this->orderId,
            $this->amount,
            $this->message,
            $this->language,
            $this->currency,
            [],
            'UTF-8',
            $this->timezone
        );

        // We should have exactly same data
        $this->assertEquals($this->expectedData, $requestData);
    }

    /**
     * Test for correctly generated request data for service 1011
     * Test keys as strings.
     */
    public function testGetPaymentRequestService1011()
    {

        // Create new protocol, with keys as strings
        $this->protocol = new IPizza(
            $this->sellerId,
            file_get_contents(__DIR__.'/../keys/IPizza/private_key.pem'),
            '',
            file_get_contents(__DIR__.'/../keys/IPizza/public_key.pem'),
            $this->requestUrl,
            $this->sellerName,
            $this->sellerAccount
        );

        // New expected values
        $this->expectedData['VK_SERVICE'] = '1011';
        $this->expectedData['VK_ACC'] = $this->sellerAccount;
        $this->expectedData['VK_NAME'] = $this->sellerName;
        $this->expectedData['VK_MAC'] = 'PuJTjADqHeArALfzTo2ZsynckTOVRFZMnOnbv9tv30KrF2a9m/yJuRn9vcd3JuaSjgzKoS7DRSouDgXAe6GNLZnduhXZrYx5JtVMmnlgooQ+/pJqO6ZOzwsEjaXooTLCCnKA5P9zWoxXpe8Al4IC9pj7jLNFG3dCeG9XO5uRZEs=';
        $this->expectedData['VK_DATETIME'] = $this->datetime;

        $requestData = $this->protocol->getPaymentRequest($this->orderId, $this->amount, $this->message, $this->language, $this->currency, [], 'UTF-8', $this->timezone);

        // We should have exactly same data
        $this->assertEquals($this->expectedData, $requestData);
    }

    /**
     * Test successful payment response.
     */
    public function testHandlePaymentResponseSuccess()
    {
        $responseData = [
            'VK_SERVICE' => '1111',
            'VK_VERSION' => '008',
            'VK_SND_ID' => $this->senderName,
            'VK_REC_ID' => $this->sellerId,
            'VK_STAMP' => $this->orderId,
            'VK_T_NO' => 100,
            'VK_AMOUNT' => $this->amount,
            'VK_CURR' => $this->currency,
            'VK_REC_ACC' => $this->sellerAccount,
            'VK_REC_NAME' => $this->sellerName,
            'VK_SND_ACC' => '101032423434543',
            'VK_SND_NAME' => 'Mart Mets',
            'VK_REF' => $this->orderId,
            'VK_MSG' => $this->message,
            'VK_MAC' => 'Sp0VzYSPyZviiCewmwbtqny8cYRcnYU4Noh0cwxOYoZ5IpQwHuolNbFI+1Kkuk5n6cWs2X48IYYOUMRi9VTqdsfSN7z5jpUwEwjLsCMDUDdro421Je7eXXkEkbZlEcgY8wtR5H+OO955aqxDdZeS0dkuuxTN70Z9Esv5feXYxsw=',
            'VK_T_DATETIME' => $this->datetime,
            'VK_LANG' => 'EST',
        ];

        $response = $this->protocol->handleResponse($responseData);

        $this->assertInstanceOf('RKD\Banklink\Response\PaymentResponse', $response);
        $this->assertEquals(PaymentResponse::STATUS_SUCCESS, $response->getStatus());

        // This is valid response
        $this->assertTrue($response->wasSuccessful());

        // We should have exactly same data
        $this->assertEquals($responseData, $response->getResponseData());

        // Order id is set to every response
        $this->assertEquals($this->orderId, $response->getOrderId());

        // We should get same prefered language
        $this->assertEquals('EST', $response->getLanguage());

        // We should get same message
        $this->assertEquals($this->message, $response->getMessage());

        $expextedSender = new \stdClass();
        $expextedSender->name = 'Mart Mets';
        $expextedSender->account = '101032423434543';

        $expextedReceiver = new \stdClass();
        $expextedReceiver->name = $this->sellerName;
        $expextedReceiver->account = $this->sellerAccount;

        // Test correct data
        $this->assertEquals($this->amount, $response->getSum());
        $this->assertEquals($this->currency, $response->getCurrency());
        $this->assertEquals($expextedSender, $response->getSender());
        $this->assertEquals($expextedReceiver, $response->getReceiver());
        $this->assertEquals(100, $response->getTransactionId());
        $this->assertEquals($this->datetime, $response->getTransactionDate());
    }

    /**
     * Test failed payment response.
     */
    public function testHandlePaymentResponseError()
    {
        $responseData = [
            'VK_SERVICE' => '1911',
            'VK_VERSION' => '008',
            'VK_SND_ID' => $this->senderName,
            'VK_REC_ID' => $this->sellerId,
            'VK_STAMP' => $this->orderId,
            'VK_REF' => $this->orderId,
            'VK_MSG' => $this->message,
            'VK_MAC' => 'o4rju0oEwITuIheUdtDjp2njKhBzvQv8RjKg+rdCB+fwGiUS8zpXzr0I+wj0vl13h+ACGAR1LO9gR2+IG1yq+AJdQdVszJIbeA1jcg1GFtl1xyLN8LXYfubHHUB/7EWwiEGZKcHrNp3pAsADlLwySQLRWatheMLPqRRk2FX96Ko=',
            'VK_DATETIME' => $this->datetime,
        ];

        $response = $this->protocol->handleResponse($responseData);

        $this->assertInstanceOf('RKD\Banklink\Response\PaymentResponse', $response);
        $this->assertEquals(PaymentResponse::STATUS_ERROR, $response->getStatus());

        // This is not valid response, so validation should fail
        $this->assertFalse($response->wasSuccessful());

        // We should have exactly same data
        $this->assertEquals($responseData, $response->getResponseData());

        // Order id is set to every response
        $this->assertEquals($this->orderId, $response->getOrderId());

        // Failed request is not settings response data
        $this->assertNull($response->getSum());
        $this->assertNull($response->getCurrency());
        $this->assertNull($response->getSender());
        $this->assertNull($response->getTransactionId());
        $this->assertNull($response->getTransactionDate());
    }

    /**
     * Test authentication request data
     * Test service 4011.
     */
    public function testGetAuthRequest4011()
    {
        $expectedData = [
            'VK_SERVICE' => '4011',
            'VK_VERSION' => '008',
            'VK_SND_ID' => 'id2000',
            'VK_RETURN' => 'http://example.com',
            'VK_DATETIME' => '2015-09-29T15:00:00+0300',
            'VK_RID' => '',
            'VK_LANG' => 'EST',
            'VK_REPLY' => '3012',
            'VK_MAC' => 'tCzsgSP0NVlNDvzsPnDZpwfPDwlrWoLFOUDSJ80sYDMbPsXBiid0M8xKT9ep0KVmj8BBUwWOGGjENSkaNXcZKAoqw0h1V1J7Hxuy1/gnIgkAkiY1OQftMYNuyrmKj1xVP4JGH3kp4ZEiyXJ0ySj/VGW4P1Vyv2oMUVHN+vDqHR0=',
        ];

        $requestData = $this->protocol->getAuthRequest();

        // We should have exactly same data
        $this->assertEquals($expectedData, $requestData);
    }

    /**
     * Test authentication request data
     * Test service 4012.
     */
    public function testGetAuthRequest4012()
    {
        $expectedData = [
            'VK_SERVICE' => '4012',
            'VK_VERSION' => '008',
            'VK_SND_ID' => 'id2000',
            'VK_REC_ID' => 'bank-id',
            'VK_NONCE' => 'random-nonce',
            'VK_RETURN' => 'http://example.com',
            'VK_DATETIME' => $this->datetime,
            'VK_RID' => 'random-rid',
            'VK_LANG' => 'EST',
            'VK_MAC' => 'MtmH+8VgmKhw/Q6kO4EZdgNMP9ZWhCXfO0OHUgyHd74ofhdkvhLnzSWxqHZgWv9lCo3ZSrZ1mHJEf1rezBod7QQDcPmMVHl9iijJug2oySgT27Re89oytVN3Zlzmko9LFEaE8JIYnvxN4B9mc/bWfW0hvHSyBehpWdlVO5HIO+c=',
        ];

        $requestData = $this->protocol->getAuthRequest('bank-id', 'random-nonce', 'random-rid');

        // We should have exactly same data
        $this->assertEquals($expectedData, $requestData);
    }

    /**
     * Test successful authentication response.
     */
    public function testHandleAuthResponseSuccess()
    {
        $responseData = [
            'VK_SERVICE' => '3013',
            'VK_VERSION' => '008',
            'VK_DATETIME' => '2015-10-12T08:47:15+0300',
            'VK_SND_ID' => 'uid100010',
            'VK_REC_ID' => 'EYP',
            'VK_RID' => 'random-rid',
            'VK_NONCE' => 'random-nonce',
            'VK_USER_NAME' => 'Tõõger Leõpäöld',
            'VK_USER_ID' => '37602294565',
            'VK_COUNTRY' => 'EE',
            'VK_OTHER' => '',
            'VK_TOKEN' => '1',
            'VK_ENCODING' => 'UTF-8',
            'VK_LANG' => 'EST',
            'VK_MAC' => 'RBkszGx+hP/B24Bziuq+vAJx0saRILcoc8BRQt8WYaq5mK6PdfOimZ3cTz9/t+4AQyZJfvA+Nv7NUxtieDKPorp4P1jzlbcR4K6lkit286H+TptIlWbPvcD2dj7Q7UapNtEB5FmMc62IMbbQCiTVyV5bs6f3DJYr3kOrOV/LHTY=',
        ];

        $response = $this->protocol->handleResponse($responseData);

        $this->assertInstanceOf('RKD\Banklink\Response\AuthResponse', $response);
        $this->assertEquals(AuthResponse::STATUS_SUCCESS, $response->getStatus());

        // This is valid response
        $this->assertTrue($response->wasSuccessful());

        // We should have exactly same data
        $this->assertEquals($responseData, $response->getResponseData());

        // We should get same prefered language
        $this->assertEquals('EST', $response->getLanguage());

        // Test user data
        $this->assertEquals($responseData['VK_USER_ID'], $response->getUserId());
        $this->assertEquals($responseData['VK_USER_NAME'], $response->getUserName());
        $this->assertEquals($responseData['VK_COUNTRY'], $response->getUserCountry());
        $this->assertEquals($responseData['VK_TOKEN'], $response->getToken());
        $this->assertEquals($responseData['VK_NONCE'], $response->getNonce());
        $this->assertEquals($responseData['VK_RID'], $response->getRid());
        $this->assertEquals($responseData['VK_DATETIME'], $response->getAuthDate());

        // Test all auth methods
        $this->assertEquals('ID card', $response->getAuthMethod());

        $response->setToken(2);
        $this->assertEquals('Mobile ID', $response->getAuthMethod());

        $response->setToken(5);
        $this->assertEquals('One-off code card', $response->getAuthMethod());

        $response->setToken(6);
        $this->assertEquals('PIN-calculator', $response->getAuthMethod());

        $response->setToken(7);
        $this->assertEquals('Code card', $response->getAuthMethod());

        $response->setToken(0);
        $this->assertEquals('unknown', $response->getAuthMethod());
    }

    /**
     * Test failed authentication response.
     */
    public function testHandleAuthResponseError()
    {
        $responseData = [
            'VK_SERVICE' => '3012',
            'VK_VERSION' => '008',
            'VK_USER' => '',
            'VK_DATETIME' => '2015-10-12T08:47:15+0300',
            'VK_SND_ID' => 'uid100010',
            'VK_REC_ID' => 'EYP',
            'VK_RID' => 'random-rid',
            'VK_NONCE' => 'random-nonce',
            'VK_USER_NAME' => 'Tõõger Leõpäöld',
            'VK_USER_ID' => '37602294565',
            'VK_COUNTRY' => 'EE',
            'VK_OTHER' => '',
            'VK_TOKEN' => '1',
            'VK_ENCODING' => 'UTF-8',
            'VK_LANG' => 'EST',
            'VK_MAC' => 'RBkszGx+hP/B24Bziuq+vAJx0saRILcoc8BRQt8WYaq5mK6PdfOimZ3cTz9/t+4AQyZJfvA+Nv7NUxtieDKPorp4P1jzlbcR4K6lkit286H+TptIlWbPvcD2dj7Q7UapNtEB5FmMc62IMbbQCiTVyV5bs6f3DJYr3kOrOV/LHTY=',
            'VK_AUTO' => 'N',
        ];

        $response = $this->protocol->handleResponse($responseData);

        $this->assertInstanceOf('RKD\Banklink\Response\AuthResponse', $response);
        $this->assertEquals(AuthResponse::STATUS_ERROR, $response->getStatus());

        // This is not valid response
        $this->assertFalse($response->wasSuccessful());

        // We should have exactly same data
        $this->assertEquals($responseData, $response->getResponseData());
    }

    public function testHandleResponseUnsupportedService()
    {
        $this->expectException(UnexpectedValueException::class);

        $responseData = [
            'VK_SERVICE' => '0000',
        ];

        $this->protocol->handleResponse($responseData);
    }

    public function testHandleResponseNoService()
    {
        $this->expectException(InvalidArgumentException::class);

        $responseData = [];
        $this->protocol->handleResponse($responseData);
    }

    public function testGetRequestFieldMissing()
    {
        $this->expectException(UnexpectedValueException::class);

        $this->protocol->getPaymentRequest(
            $this->orderId,
            $this->amount,
            $this->message,
            $this->language,
            $this->currency,
            [
                'VK_STAMP' => null
            ],
            'UTF-8',
            $this->timezone
        );
    }

    /**
     * Test can't generate request inputs.
     */
    public function testNoRequestData()
    {
        $this->expectException(UnexpectedValueException::class);

        $request = new PaymentRequest('https://google.com', []);

        $request->getRequestInputs();
    }

    /**
     * Test invalid public key.
     */
    public function testInvalidPublicKey()
    {
        $this->expectException(UnexpectedValueException::class);

        $this->protocol = new IPizza(
            $this->sellerId,
            __DIR__.'/../keys/IPizza/private_key.pem',
            '',
            __DIR__.'/../keys/IPizza/no_key.pem',
            $this->requestUrl,
            $this->sellerName,
            $this->sellerAccount
        );

        $responseData = [
            'VK_SERVICE' => '3013',
            'VK_VERSION' => '008',
            'VK_DATETIME' => '2015-10-12T08:47:15+0300',
            'VK_SND_ID' => 'uid100010',
            'VK_REC_ID' => 'EYP',
            'VK_RID' => 'random-rid',
            'VK_NONCE' => 'random-nonce',
            'VK_USER_NAME' => 'Error here',
            'VK_USER_ID' => '37602294565',
            'VK_COUNTRY' => 'EE',
            'VK_OTHER' => '',
            'VK_TOKEN' => '1',
            'VK_ENCODING' => 'UTF-8',
            'VK_LANG' => 'EST',
            'VK_MAC' => 'RBkszGx+hP/B24Bziuq+vAJx0saRILcoc8BRQt8WYaq5mK6PdfOimZ3cTz9/t+4AQyZJfvA+Nv7NUxtieDKPorp4P1jzlbcR4K6lkit286H+TptIlWbPvcD2dj7Q7UapNtEB5FmMc62IMbbQCiTVyV5bs6f3DJYr3kOrOV/LHTY=',
        ];

        $this->protocol->handleResponse($responseData);
    }

    /**
     * Test invalid private key.
     */
    public function testInvalidPrivateKey()
    {
        $this->expectException(UnexpectedValueException::class);

        $this->protocol = new IPizza(
            $this->sellerId,
            __DIR__.'/../keys/IPizza/no_key.pem',
            '',
            __DIR__.'/../keys/IPizza/public_key.pem',
            $this->requestUrl,
            $this->sellerName,
            $this->sellerAccount
        );

        $this->protocol->getAuthRequest();
    }

    /**
     * Test that we can change algorithm
     */
    public function testSetAlgorithm()
    {
        $this->protocol->setAlgorithm(OPENSSL_ALGO_SHA256);
        $this->assertEquals(OPENSSL_ALGO_SHA256, $this->protocol->getAlgorithm());
    }

    /**
     * Test that we can change algorithm
     */
    public function testGeneratesCorrectMacWithSHA256()
    {
        $this->protocol = new IPizza(
            $this->sellerId,
            __DIR__.'/../keys/IPizza/private_key_sha256.pem',
            '',
            __DIR__.'/../keys/IPizza/public_key_sha256.pem',
            $this->requestUrl,
            $this->sellerName,
            $this->sellerAccount
        );

        $this->protocol->setAlgorithm(OPENSSL_ALGO_SHA256);

        $requestData = $this->protocol->getAuthRequest();

        // Must match with SHA256 encrypted VK_MAC
        $this->assertEquals(
            'BoHSS+z3syMAU0Vi/Ob8lTS8FIMAZ6ZslYnjNXZVEZEn2aXuc/1L2oR/Ef8DvPFJ1ocOjxOHiQ0QJruD5CpiDXI3/hxSJ2qJg0a0HezrTPgc6iVONcsas62+PBlpWFSnZ9u5qg1eETnHgYzjtBZE2FzqWJWC2UuMUxn9uGcGhoxd1wGCrgc2zu4Ub540hhEyoUAJyjN5xA89nKb8H0tY58s96uYM9G8Isj8cDWVICFI4Q5O3Rn56sfhBEyNrSOwMCukf+zsIfoQtt3qto9JZ/IZ6Znl8ze8LCZqwvqnFiRrkXnVwvPI7aiyCIvFccqJiUsl5ahqpXrnFJyt2kyAGDQ==',
            $requestData['VK_MAC']
        );
    }

    public function testCanHandleSHA256AlgoResponse()
    {
        $this->protocol = new IPizza(
            $this->sellerId,
            __DIR__.'/../keys/IPizza/private_key_sha256.pem',
            '',
            __DIR__.'/../keys/IPizza/public_key_sha256.pem',
            $this->requestUrl,
            $this->sellerName,
            $this->sellerAccount
        );

        $this->protocol->setAlgorithm(OPENSSL_ALGO_SHA256);

        $responseData = [
            "VK_SERVICE" => "1111",
            "VK_VERSION" => "008",
            "VK_SND_ID" => "Pocopay",
            "VK_REC_ID" => "Redwall",
            "VK_STAMP" => "1476279858",
            "VK_REF" => "324234234",
            "VK_MSG" => "Example payment",
            "VK_ENCODING" => "UTF-8",
            "VK_LANG" => "EST",
            "VK_AUTO" => "N",
            "VK_CURR" => "EUR",
            "VK_REC_ACC" => "EE439999000010127858",
            "VK_REC_NAME" => "REDWALL",
            "VK_AMOUNT" => "1.00",
            "VK_MAC" => "k0UjizlWrG75i6euZMbzVPp4gaA+PxiTT+bi1WRdVmi9XNZ11EA80WFVRqEBcx187E8otmdauGyVWzHbbOSBzLuGuoHVD5D8n6M/PnpkNrHRQan//bql5kLzJm82togfTGQEa775s1kL7rQPqMbOYN7jIdOvDvgSxlHhLwgQ8G7Vnk1t1JH7nmoUxXgrcJqPtxBaevSa2fOkFE90+cuJ9CZUaRr76emASNvs1SxwHpuW782OCXbcwuTdJo+KSjWG2Hi2phVvYXVQlqNROzac921DXEatOoBYA0EbpsMMDmPAMyartnGiCd3owLtFfgHbnV4ZZJ02IjsWtOJu4n41fg==",
            "VK_T_NO" => "10117822",
            "VK_SND_ACC" => "EE529999000010128569",
            "VK_SND_NAME" => "RENE KORSS",
            "VK_T_DATETIME" => "2019-01-15T18:10:04+0000",
        ];

        $response = $this->protocol->handleResponse($responseData);

        $this->assertTrue($response->wasSuccessful());
    }
}
