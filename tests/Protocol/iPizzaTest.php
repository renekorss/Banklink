<?php
namespace RKD\Banklink\iPizza;

use RKD\Banklink\Protocol\Helper\ProtocolHelper;
use RKD\Banklink\Protocol\iPizza;
use RKD\Banklink\Response\PaymentResponse;

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

    public function testGetPaymentRequestData(){

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
        );

        $responseData = $this->protocol->getPaymentRequestData($this->orderId, $this->amount, $this->message, 'UTF-8', $this->language, $this->currency, $this->timezone);

        $this->assertEquals($expectedData, $responseData);

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

        // New expected values
        $expectedData['VK_SERVICE']  = '1011';
        $expectedData['VK_ACC']      = $this->sellerAccount;
        $expectedData['VK_NAME']     = $this->sellerName;
        $expectedData['VK_MAC']      = 'RkwVzvbKGTzwg3xeue2/CPDA82nGP2I8O8DChcdkQ7PdiB1p7wLkRVEIeF6sJKeqx13HQftRtTlKMpbfr9/hdO3h6zZcc7qIT9GVXQBH38Ub+D0YuF9hEGmVLToJFXxequUfdd6W77l61TplDYYeHt+5ZI/kkxWg/mmpV38WmfU=';
        $expectedData['VK_DATETIME'] = $this->datetime;

        $responseData = $this->protocol->getPaymentRequestData($this->orderId, $this->amount, $this->message, 'UTF-8', $this->language, $this->currency, $this->timezone);

        $this->assertEquals($expectedData, $responseData);
    }

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
     * @expectedException UnexpectedValueException
     */

    public function testHandlePaymentResponseUnsupportedService(){
        $responseData = array(
            'VK_SERVICE'  => '0000',
        );

        $response = $this->protocol->handleResponse($responseData);
    }
}
