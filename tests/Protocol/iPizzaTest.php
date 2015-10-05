<?php
namespace RKD\Banklink\iPizza;

use RKD\Banklink;
use RKD\Banklink\Protocol\Helper\ProtocolHelper;
use RKD\Banklink\Protocol\iPizza;
use RKD\Banklink\Response\PaymentResponse;
use RKD\Banklink\Request\PaymentRequest;

/**
 * Test suite for iPizza protocol
 *
 * @author  Rene Korss <rene.korss@gmail.com>
 */

class iPizzaTest extends \PHPUnit_Framework_TestCase{

    private $protocol;

    private $sellerId;
    private $sellerName;
    private $sellerAccount;

    private $senderName;

    private $orderId;
    private $amount;
    private $message;
    private $language;
    private $currency;
    private $timezone;
    private $datetime;

    private $requestUrl;

    public function setUp(){


        $this->sellerId      = 'id2000';
        $this->sellerAccount = '1010342342354345435';
        $this->sellerName    = 'Ülo Pääsuke';

        $this->senderName    = 'Toomas Jäär';

        $this->orderId       = 100;
        $this->amount        = 10.00;
        $this->message       = 'First payment';
        $this->language      = 'EST';
        $this->currency      = 'EUR';
        $this->timezone      = 'Europe/Tallinn';

        // From ENV variable
        $this->datetime      = getenv('TEST_DATETIME');

        $this->requestUrl   = 'http://example.com';

        $this->protocol = new iPizza(
            $this->sellerId,
            __DIR__.'/../keys/iPizza/private_key.pem',
            '',
            __DIR__.'/../keys/iPizza/public_key.pem',
            $this->requestUrl
        );
    }

    /**
     * Test for correctly generated request data
     */

    public function testGetPaymentRequest(){

        // Create new banklink instance
        $seb = new Banklink\SEB($this->protocol);

        // Test service 1012
        $expectedData = array(
            'VK_SERVICE'  => '1012',
            'VK_VERSION'  => '008',
            'VK_SND_ID'   => $this->sellerId,
            'VK_STAMP'    => $this->orderId,
            'VK_AMOUNT'   => $this->amount,
            'VK_CURR'     => $this->currency,
            'VK_REF'      => ProtocolHelper::calculateReference($this->orderId),
            'VK_MSG'      => $this->message,
            'VK_RETURN'   => $this->requestUrl,
            'VK_CANCEL'   => $this->requestUrl,
            'VK_LANG'     => $this->language,
            'VK_MAC'      => 'PmAB256IR1FzTKZHNn5LBPso/KyLAhNcTOMq82lhpYn0mXKYtVtpNkolQxyETnTcIn1TcYOmekJEATe86Bz2MRljEQqllkaIl7bNuLCtuBPtAOYWNLmQHoop+5QSiguJEmEV+JJU3w4BApjWcsHA5HYlYze+3L09UO6na0lB/Zs=',
            'VK_DATETIME' => $this->datetime,
            'VK_ENCODING' => 'ISO-8859-1'
        );

        $request = $seb->getPaymentRequest($this->orderId, $this->amount, $this->message, $this->language, $this->currency, $this->timezone);

        // Instance of PaymentRequest and data is same
        $this->assertInstanceOf('RKD\Banklink\Request\PaymentRequest', $request);
        $this->assertEquals($expectedData, $request->getRequestData());

        // Production env url
        $this->assertEquals('https://www.seb.ee/cgi-bin/unet3.sh/un3min.r', $request->getRequestUrl());

        // Test service 1011
        $this->protocol = new iPizza(
            $this->sellerId,
            __DIR__.'/../keys/iPizza/private_key.pem',
            '',
            __DIR__.'/../keys/iPizza/public_key.pem',
            $this->requestUrl,
            $this->sellerName,
            $this->sellerAccount
        );

        // Create new banklink instance
        $seb = new Banklink\SEB($this->protocol, true);

        // New expected values
        $expectedData['VK_SERVICE']  = '1011';
        $expectedData['VK_ACC']      = $this->sellerAccount;
        $expectedData['VK_NAME']     = $this->sellerName;
        $expectedData['VK_MAC']      = 'RkwVzvbKGTzwg3xeue2/CPDA82nGP2I8O8DChcdkQ7PdiB1p7wLkRVEIeF6sJKeqx13HQftRtTlKMpbfr9/hdO3h6zZcc7qIT9GVXQBH38Ub+D0YuF9hEGmVLToJFXxequUfdd6W77l61TplDYYeHt+5ZI/kkxWg/mmpV38WmfU=';
        $expectedData['VK_DATETIME'] = $this->datetime;

        $request = $seb->getPaymentRequest($this->orderId, $this->amount, $this->message, $this->language, $this->currency, $this->timezone);

        // Instance of PaymentRequest and data is same
        $this->assertInstanceOf('RKD\Banklink\Request\PaymentRequest', $request);
        $this->assertEquals($expectedData, $request->getRequestData());

        // Test env url
        $this->assertEquals('http://localhost:8080/banklink/seb-common', $request->getRequestUrl());

        // Create new banklink instance
        $seb = new Banklink\SEB($this->protocol, false, 'http://google.com');

        $request = $seb->getPaymentRequest($this->orderId, $this->amount, $this->message, $this->language, $this->currency, $this->timezone);

        // Get same data again, already exists
        $request = $seb->getPaymentRequest($this->orderId, $this->amount, $this->message, $this->language, $this->currency, $this->timezone);

        // Custom url
        $this->assertEquals('http://google.com', $request->getRequestUrl());

        // Get HTML
        $this->assertContains('<input type="hidden"', $request->getRequestInputs());
    }

    /**
     * Test successful payment response
     */

    public function testHandlePaymentResponseSuccess(){
        $responseData = array(
            'VK_SERVICE'    => '1111',
            'VK_VERSION'    => '008',
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
            'VK_MAC'        => 'qtOjJvtRymP54/Xua+W75JADgq5Dc/lMpVnzA9nv9GP7n75VPKeHsKI07ok0XnY1fCeRHms2E+PKilgq8JzTUF80oTR1Jtt2OqW/IzGxoxMbmhmFGLR45W+3KmmcPOl6E95ZwjwF9cFe9NPsl/4RwvsKeOad5XeidaNsS43EHoY=',
            'VK_T_DATETIME' => $this->datetime,
        );

        $response = $this->protocol->handleResponse($responseData);

        $this->assertEquals(PaymentResponse::STATUS_SUCCESS, $response->getStatus());
    }

    /**
     * Test failed payment response
     */

    public function testHandlePaymentResponseError(){
        $responseData = array(
            'VK_SERVICE'  => '1911',
            'VK_VERSION'  => '008',
            'VK_SND_ID'   => $this->senderName,
            'VK_REC_ID'   => $this->sellerId,
            'VK_STAMP'    => $this->orderId,
            'VK_REF'      => $this->orderId,
            'VK_MSG'      => $this->message,
            'VK_MAC'      => 'o4rju0oEwITuIheUdtDjp2njKhBzvQv8RjKg+rdCB+fwGiUS8zpXzr0I+wj0vl13h+ACGAR1LO9gR2+IG1yq+AJdQdVszJIbeA1jcg1GFtl1xyLN8LXYfubHHUB/7EWwiEGZKcHrNp3pAsADlLwySQLRWatheMLPqRRk2FX96Ko=',
            'VK_DATETIME' => $this->datetime,
        );

        $response = $this->protocol->handleResponse($responseData);

        $this->assertEquals(PaymentResponse::STATUS_ERROR, $response->getStatus());
    }

    /**
     * Test public and private keys as strings
     */

    public function testKeysAsStrings(){

        $this->protocol = new iPizza(
            $this->sellerId,
            file_get_contents(__DIR__.'/../keys/iPizza/private_key.pem'),
            '',
            file_get_contents(__DIR__.'/../keys/iPizza/public_key.pem'),
            $this->requestUrl,
            $this->sellerName,
            $this->sellerAccount
        );

        $expectedData = array(
            'VK_SERVICE'  => '1011',
            'VK_VERSION'  => '008',
            'VK_SND_ID'   => $this->sellerId,
            'VK_STAMP'    => $this->orderId,
            'VK_AMOUNT'   => $this->amount,
            'VK_CURR'     => $this->currency,
            'VK_REF'      => ProtocolHelper::calculateReference($this->orderId),
            'VK_MSG'      => $this->message,
            'VK_RETURN'   => $this->requestUrl,
            'VK_CANCEL'   => $this->requestUrl,
            'VK_LANG'     => $this->language,
            'VK_MAC'      => 'RkwVzvbKGTzwg3xeue2/CPDA82nGP2I8O8DChcdkQ7PdiB1p7wLkRVEIeF6sJKeqx13HQftRtTlKMpbfr9/hdO3h6zZcc7qIT9GVXQBH38Ub+D0YuF9hEGmVLToJFXxequUfdd6W77l61TplDYYeHt+5ZI/kkxWg/mmpV38WmfU=',
            'VK_DATETIME' => $this->datetime,
            'VK_ACC'      => $this->sellerAccount,
            'VK_NAME'     => $this->sellerName,
            'VK_DATETIME' => $this->datetime

        );

        $requestData = $this->protocol->getPaymentRequest($this->orderId, $this->amount, $this->message, 'UTF-8', $this->language, $this->currency, $this->timezone);

        $this->assertEquals($expectedData, $requestData);

        // Example bank response data
        $responseData = array(
            'VK_SERVICE'  => '1111',
            'VK_VERSION'  => '008',
            'VK_SND_ID'   => 'Tarmo Tamm',
            'VK_REC_ID'   => $this->sellerId,
            'VK_STAMP'    => $this->orderId,
            'VK_T_NO'     => 1,
            'VK_AMOUNT'   => $this->amount,
            'VK_CURR'     => $this->currency,
            'VK_REC_ACC'  => '10103434345345',
            'VK_REC_NAME' => 'Tarmo Tamm',
            'VK_SND_ACC'  => $this->sellerAccount,
            'VK_SND_NAME' => $this->sellerName,
            'VK_REF'      => ProtocolHelper::calculateReference($this->orderId),
            'VK_MSG'      => $this->message,
            'VK_MAC'      => 'qtOjJvtRymP54/Xua+W75JADgq5Dc/lMpVnzA9nv9GP7n75VPKeHsKI07ok0XnY1fCeRHms2E+PKilgq8JzTUF80oTR1Jtt2OqW/IzGxoxMbmhmFGLR45W+3KmmcPOl6E95ZwjwF9cFe9NPsl/4RwvsKeOad5XeidaNsS43EHoY=',
            'VK_T_DATETIME' => $this->datetime
        );

        $response = $this->protocol->handleResponse($responseData);
        $this->assertInstanceOf('RKD\Banklink\Response\PaymentResponse', $response);

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
     * @expectedException UnexpectedValueException
     */

    public function testHandlePaymentResponseUnsupportedService(){
        $responseData = array(
            'VK_SERVICE'  => '0000',
        );

        $response = $this->protocol->handleResponse($responseData);
    }

    /**
     * @expectedException UnexpectedValueException
     */

    public function testGetPaymentRequestFieldMissing(){
        $responseData = $this->protocol->getPaymentRequest($this->orderId, '', $this->message, 'UTF-8', $this->language, $this->currency, $this->timezone);
    }

    /**
     * Test can't generate request inputs
     *
     * @expectedException UnexpectedValueException
     */

    public function testNoRequestData(){
        $request = new PaymentRequest('http://google.com', array());

        $request->getRequestInputs();
    }
}
