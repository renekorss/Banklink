<?php

namespace RKD\Banklink\Test\Protocol;

use RKD\Banklink\Protocol\Helper\ProtocolHelper;
use RKD\Banklink\Protocol\IPizza2015;
use RKD\Banklink\Response\PaymentResponse;

/**
 * Test suite for IPizza 2015 protocol.
 *
 * @author  Rene Korss <rene.korss@gmail.com>
 */
class IPizza2015Test extends IPizzaTest
{
    /**
     * Set test data.
     */
    public function setUp() : void
    {
        parent::setUp();

        $this->protocol = new IPizza2015(
            $this->sellerId,
            __DIR__.'/../keys/IPizza/private_key.pem',
            '',
            __DIR__.'/../keys/IPizza/public_key.pem',
            $this->requestUrl
        );

        // Test data
        $this->expectedData = [
            'VK_SERVICE' => '1002',
            'VK_VERSION' => '008',
            'VK_SND_ID' => $this->sellerId,
            'VK_STAMP' => $this->orderId,
            'VK_AMOUNT' => $this->amount,
            'VK_CURR' => $this->currency,
            'VK_REF' => ProtocolHelper::calculateReference($this->orderId),
            'VK_MSG' => $this->message,
            'VK_RETURN' => $this->requestUrl,
            'VK_LANG' => $this->language,
            'VK_MAC' => 'WnPfkNEbj0lvkdrVeE7j1E8mGWPs1R+mX/JPokmxHXXjDmzrwlY2TfGmQLYqdog6k1m4By4C+olSeZ/BN98UaXgmeonZgtTrrrOttYT1KaK5DfOTa3VptjqzG1k7jNDloFOJft4fYCrxx/A7XUJFXpKhIMf0lpeLkHOuW2x3U3k='
        ];
    }

    /**
     * Test for correctly generated request data for service 1011
     * Test keys as strings.
     */
    public function testGetPaymentRequestService1011()
    {

        // Create new protocol, with keys as strings
        $this->protocol = new IPizza2015(
            $this->sellerId,
            file_get_contents(__DIR__.'/../keys/IPizza/private_key.pem'),
            '',
            file_get_contents(__DIR__.'/../keys/IPizza/public_key.pem'),
            $this->requestUrl,
            $this->sellerName,
            $this->sellerAccount
        );

        // New expected values
        $this->expectedData['VK_SERVICE'] = '1001';
        $this->expectedData['VK_ACC'] = $this->sellerAccount;
        $this->expectedData['VK_NAME'] = $this->sellerName;
        $this->expectedData['VK_MAC'] = 'NYD8MmlHeNaAmmYx8PRmYemc5gOi+7I8DZRNnLW3m4GW0hxnbhWxMVP1vTd0hS6t96k8e8CnTpPMTnY9Om2wEOXvT6xnGfwZseUnMdYLgmxhOh4+fUUaMFagTDibwi37Jm3JfhdmL+bhoWtDLw5sbxUzgVP0N7N2m5xe2sODxeI=';

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
            'VK_SERVICE' => '1101',
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
            'VK_MAC' => 'nlIyuBx4d3jOZd3pnZz7kvxP0p+HqnP2n8Tzbm5SDlPItWdFlLYRuHrMwhd0DpzfBeXay0DvMeLjCN1ZOeWqXxEe6COaXILm4k/G4NW2MrieznH8bOmJAJjaKGQiGxJv46zNzJpxbzzr0kcG2WGJQvY8JsZZWOUbPNsVBcIwBHg=',
            'VK_T_DATE' => $this->datetime,
            'VK_LANG' => 'EST',
        ];

        $response = $this->protocol->handleResponse($responseData);

        $this->assertInstanceOf('RKD\Banklink\Response\PaymentResponse', $response);
        $this->assertSame(PaymentResponse::STATUS_SUCCESS, $response->getStatus());

        // This is valid response
        $this->assertTrue($response->wasSuccessful());

        // We should have exactly same data
        $this->assertEquals($responseData, $response->getResponseData());

        // Order id is set to every response
        $this->assertEquals($this->orderId, $response->getOrderId());

        // We should get same prefered language
        $this->assertSame('EST', $response->getLanguage());

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
        $this->assertSame('100', $response->getTransactionId());
        $this->assertEquals($this->datetime, $response->getTransactionDate());
    }

    /**
     * Test failed payment response.
     */
    public function testHandlePaymentResponseError()
    {
        $responseData = [
            'VK_SERVICE' => '1901',
            'VK_VERSION' => '008',
            'VK_SND_ID' => $this->senderName,
            'VK_REC_ID' => $this->sellerId,
            'VK_STAMP' => $this->orderId,
            'VK_REF' => $this->orderId,
            'VK_MSG' => $this->message,
            'VK_MAC' => 'o4rju0oEwITuIheUdtDjp2njKhBzvQv8RjKg+rdCB+fwGiUS8zpXzr0I+wj0vl13h+ACGAR1LO9gR2+IG1yq+AJdQdVszJIbeA1jcg1GFtl1xyLN8LXYfubHHUB/7EWwiEGZKcHrNp3pAsADlLwySQLRWatheMLPqRRk2FX96Ko=',
            'VK_DATE' => $this->datetime,
        ];

        $response = $this->protocol->handleResponse($responseData);

        $this->assertInstanceOf('RKD\Banklink\Response\PaymentResponse', $response);
        $this->assertSame(PaymentResponse::STATUS_ERROR, $response->getStatus());

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
     *
     * @group ignore
     */
    public function testGetAuthRequest4011()
    {
    }

    /**
     * Test authentication request data
     * Test service 4012.
     *
     * @group ignore
     */
    public function testGetAuthRequest4012()
    {
    }

    /**
     * Test successful authentication response.
     *
     * @group ignore
     */
    public function testHandleAuthResponseSuccess()
    {
    }

    /**
     * Test failed authentication response.
     *
     * @group ignore
     */
    public function testHandleAuthResponseError()
    {
    }
}
