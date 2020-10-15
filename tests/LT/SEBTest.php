<?php

namespace RKD\Banklink\Test\LT;

use RKD\Banklink\Response\PaymentResponse;

/**
 * Test suite for SEB LT banklink.
 *
 * @author  Rene Korss <rene.korss@gmail.com>
 */
class SEBTest extends \RKD\Banklink\Test\EE\SEBTest
{
    protected $bankClass = "RKD\Banklink\LT\SEB";
    protected $protocolClass = "RKD\Banklink\Protocol\IPizza2015";

    protected $requestUrl = [
        'payment' => 'https://e.seb.lt/mainib/web.p',
        'auth' => ''
    ];
    protected $testRequestUrl = [
        'payment' => 'https://e.seb.lt/mainib/web.p',
        'auth' => ''
    ];

    /**
     * Set test data.
     */
    public function setUp() : void
    {
        parent::setUp();

        $this->expectedData['VK_SERVICE'] = '1001';
        $this->expectedData['VK_NAME'] = $this->sellerName;
        $this->expectedData['VK_ACC'] = $this->sellerAccount;
        $this->expectedData['VK_MAC'] = 'NYD8MmlHeNaAmmYx8PRmYemc5gOi+7I8DZRNnLW3m4GW0hxnbhWxMVP1vTd0hS6t96k8e8CnTpPMTnY9Om2wEOXvT6xnGfwZseUnMdYLgmxhOh4+fUUaMFagTDibwi37Jm3JfhdmL+bhoWtDLw5sbxUzgVP0N7N2m5xe2sODxeI=';

        unset($this->expectedData['VK_CANCEL']);
        unset($this->expectedData['VK_ENCODING']);
        unset($this->expectedData['VK_DATETIME']);

        $this->setUpBanklinkWithSeller();
    }

    /**
     * Test for correctly generated request data for service 1001.
     */
    public function testGetPaymentRequestService1012()
    {
        // Test service 1001
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
        $this->expectedData['VK_SERVICE']  = '1001';
        $this->expectedData['VK_ACC']      = $this->sellerAccount;
        $this->expectedData['VK_NAME']     = $this->sellerName;
        $this->expectedData['VK_MAC']      = 'NYD8MmlHeNaAmmYx8PRmYemc5gOi+7I8DZRNnLW3m4GW0hxnbhWxMVP1vTd0hS6t96k8e8CnTpPMTnY9Om2wEOXvT6xnGfwZseUnMdYLgmxhOh4+fUUaMFagTDibwi37Jm3JfhdmL+bhoWtDLw5sbxUzgVP0N7N2m5xe2sODxeI=';

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
            'VK_SERVICE'    => '1101',
            'VK_VERSION'    => '008',
            'VK_SND_ID'     => $this->senderName,
            'VK_REC_ID'     => $this->sellerId,
            'VK_STAMP'      => $this->orderId,
            'VK_T_NO'       => 100,
            'VK_AMOUNT'     => $this->amount,
            'VK_CURR'       => $this->currency,
            'VK_ACC'        => $this->sellerAccount,
            'VK_REC_NAME'   => $this->sellerName,
            'VK_SND_ACC'    => '101032423434543',
            'VK_SND_NAME'   => 'Mart Mets',
            'VK_REF'        => $this->orderId,
            'VK_MSG'        => $this->message,
            'VK_MAC'        => 'nlIyuBx4d3jOZd3pnZz7kvxP0p+HqnP2n8Tzbm5SDlPItWdFlLYRuHrMwhd0DpzfBeXay0DvMeLjCN1ZOeWqXxEe6COaXILm4k/G4NW2MrieznH8bOmJAJjaKGQiGxJv46zNzJpxbzzr0kcG2WGJQvY8JsZZWOUbPNsVBcIwBHg=',
            'VK_T_DATE'     => $this->datetime,
            'VK_AUTO'       => 'Y'
        ];

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
            'VK_SERVICE'  => '1901',
            'VK_VERSION'  => '008',
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
     * Test failed payment response.
     */
    public function testHandlePaymentResponseError1201()
    {
        $responseData = [
            'VK_SERVICE'  => '1201',
            'VK_VERSION'  => '008',
            'VK_SND_ID'   => $this->senderName,
            'VK_REC_ID'   => $this->sellerId,
            'VK_STAMP'    => $this->orderId,
            'VK_AMOUNT'   => $this->amount,
            'VK_CURR'     => $this->currency,
            'VK_ACC'      => $this->sellerAccount,
            'VK_REC_NAME' => $this->sellerName,
            'VK_SND_ACC'  => '101032423434543',
            'VK_SND_NAME' => 'Mart Mets',
            'VK_REF'      => $this->orderId,
            'VK_MSG'      => $this->message,
            'VK_MAC'      => 'o4rju0oEwITuIheUdtDjp2njKhBzvQv8RjKg+rdCB+fwGiUS8zpXzr0I+wj0vl13h+ACGAR1LO9gR2+IG1yq+AJdQdVszJIbeA1jcg1GFtl1xyLN8LXYfubHHUB/7EWwiEGZKcHrNp3pAsADlLwySQLRWatheMLPqRRk2FX96Ko=',
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
     * Test custom request data
     */
    public function testCustomRequestData()
    {
        $this->bank = new $this->bankClass($this->protocol);

        $customData = [
            'INAPP' => 1, // new data
            'VK_REF' => 'mycustomref', // override data
            'VK_MAC' => 'hYw5OAS0rp6evyFCDdqrfSnTKhXACIE/pBhOo3MjY6F+YfE/sVvLcTDkLTcDWjKeCDs5ze7qNPC3+DWT1i9HGwjUcEJtnV3QRW2rEN4Zxy3rbtodsuDfj3CHJA5dq1Vx9YXFyPqsQrq8segH+PQtg4jQYwCffKTb585bBHuNiuU='
        ];

        $this->expectedData = array_merge($this->expectedData, $customData);

        $request = $this->bank->getPaymentRequest($this->orderId, $this->amount, $this->message, $this->language, $this->currency, $customData, $this->timezone);

        // Custom url
        $this->assertEquals($this->expectedData, $request->getRequestData());
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
}
