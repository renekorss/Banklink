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
    private $expectedData;

    private $requestUrl;

    /**
     * Set test data
     */

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

        $this->requestUrl    = 'http://example.com';

        $this->protocol = new iPizza(
            $this->sellerId,
            __DIR__.'/../keys/iPizza/private_key.pem',
            '',
            __DIR__.'/../keys/iPizza/public_key.pem',
            $this->requestUrl
        );

        // Test data
        $this->expectedData = array(
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
            'VK_DATETIME' => $this->datetime
        );
    }

    /**
     * Test for correctly generated request data for service 1012
     */

    public function testGetPaymentRequestService1012(){

        // Test service 1012
        $requestData = $this->protocol->getPaymentRequest($this->orderId, $this->amount, $this->message, 'UTF-8', $this->language, $this->currency, $this->timezone);

        // We should have exactly same data
        $this->assertEquals($this->expectedData, $requestData);

    }

    /**
     * Test for correctly generated request data for service 1011
     * Test keys as strings
     */

    public function testGetPaymentRequestService1011(){

        // Create new protocol, with keys as strings
        $this->protocol = new iPizza(
            $this->sellerId,
            file_get_contents(__DIR__.'/../keys/iPizza/private_key.pem'),
            '',
            file_get_contents(__DIR__.'/../keys/iPizza/public_key.pem'),
            $this->requestUrl,
            $this->sellerName,
            $this->sellerAccount
        );

        // New expected values
        $this->expectedData['VK_SERVICE']  = '1011';
        $this->expectedData['VK_ACC']      = $this->sellerAccount;
        $this->expectedData['VK_NAME']     = $this->sellerName;
        $this->expectedData['VK_MAC']      = 'RkwVzvbKGTzwg3xeue2/CPDA82nGP2I8O8DChcdkQ7PdiB1p7wLkRVEIeF6sJKeqx13HQftRtTlKMpbfr9/hdO3h6zZcc7qIT9GVXQBH38Ub+D0YuF9hEGmVLToJFXxequUfdd6W77l61TplDYYeHt+5ZI/kkxWg/mmpV38WmfU=';
        $this->expectedData['VK_DATETIME'] = $this->datetime;

        $requestData = $this->protocol->getPaymentRequest($this->orderId, $this->amount, $this->message, 'UTF-8', $this->language, $this->currency, $this->timezone);

        // We should have exactly same data
        $this->assertEquals($this->expectedData, $requestData);
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
            'VK_T_DATETIME' => $this->datetime
        );

        $response = $this->protocol->handleResponse($responseData);

        $this->assertInstanceOf('RKD\Banklink\Response\PaymentResponse', $response);
        $this->assertEquals(PaymentResponse::STATUS_SUCCESS, $response->getStatus());

        // This is valid response
        $this->assertTrue($response->wasSuccessful());

        // We should have exactly same data
        $this->assertEquals($responseData, $response->getResponseData());

        // Order id is set to every response
        $this->assertEquals($this->orderId, $response->getOrderId());

        $expextedSender          = new \stdClass();
        $expextedSender->name    = 'Mart Mets';
        $expextedSender->account = '101032423434543';

        // Test correct data
        $this->assertEquals($this->amount, $response->getSum());
        $this->assertEquals($this->currency, $response->getCurrency());
        $this->assertEquals($expextedSender, $response->getSender());
        $this->assertEquals(100, $response->getTransactionId());
        $this->assertEquals($this->datetime, $response->getTransactionDate());

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
