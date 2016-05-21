<?php

namespace RKD\BanklinkTests;

use RKD\Banklink;
use RKD\Banklink\Protocol\ECommerce;
use RKD\Banklink\Response\PaymentResponse;
use RKD\Banklink\Request\PaymentRequest;

/**
 * Test suite for Estcard payment gateway.
 *
 * @author  Rene Korss <rene.korss@gmail.com>
 */
class EstcardTest extends \PHPUnit_Framework_TestCase
{
    protected $bankClass = "RKD\Banklink\Estcard";

    protected $requestUrl = 'https://pos.estcard.ee/test-pos/iPayServlet';
    protected $testRequestUrl = 'http://localhost:8080/banklink/ec';

    /**
     * Set test data.
     */
    public function setUp()
    {
        $this->sellerId = 'uid100081';
        $this->sellerName = 'Ülo Pääsuke';
        $this->sellerAccount = '1010342342354345435';

        $this->senderName = 'Toomas Jäär';

        $this->orderId = 201605861819;
        $this->amount = 150;
        $this->message = 'Test makse';
        $this->language = 'EST';
        $this->currency = 'EUR';
        $this->timezone = 'Europe/Tallinn';

        // From ENV variable
        $this->datetime = getenv('TEST_DATETIME');

        $datetime = new \Datetime($this->datetime, new \DateTimeZone($this->timezone));
        $this->expectedDate = $datetime->format('YmdHis');

        $this->customRequestUrl = 'http://example.com';

        $this->protocol = new ECommerce(
            $this->sellerId,
            __DIR__.'/keys/IPizza/private_key.pem',
            '',
            __DIR__.'/keys/IPizza/public_key.pem',
            $this->customRequestUrl
        );


        $this->estcard = new $this->bankClass($this->protocol);

        $this->expectedData = array(
            'action' => 'gaf',
            'ver' => '004',
            'id' => $this->sellerId,
            'ecuno' => $this->orderId,
            'eamount' => $this->amount * 100,
            'cur' => $this->currency,
            'additionalinfo' => $this->message,
            'feedBackUrl' => $this->customRequestUrl,
            'lang' => 'et',
            'delivery' => 'S',
            'mac' => 'aaacb942dd3512d915224d244c20862457284e72587057d182ee1ee1b6da1082b43632cf9a9138144f52b48edc6fe8cdeb2193320f7a651c670c3550c92ae619c8fd33713f313d8c88241ec8322c78831bb818715eee3584ed612891ea4ce7a31398d280aa7b878907a7f6a2915629a4d369ddd2b1c0b56ad8dec19f5fafb35f',
            'datetime' => $this->expectedDate,
            'charEncoding' => 'UTF-8',
        );
    }

    /**
     * Test for correctly generated request data.
     */
    public function testGetPaymentRequest()
    {

        // Test service 1012
        $request = $this->estcard->getPaymentRequest($this->orderId, $this->amount, $this->message, $this->language, $this->currency, $this->timezone);

        // Instance of PaymentRequest and data is same
        $this->assertInstanceOf('RKD\Banklink\Request\PaymentRequest', $request);
        $this->assertEquals($this->expectedData, $request->getRequestData());

        // Production env url
        $this->assertEquals($this->requestUrl, $request->getRequestUrl());
    }

   /**
     * Test successful payment response.
     */
    public function testHandlePaymentResponseSuccess()
    {
        $responseData = array(
            'action' => 'afb',
            'ver' => '4',
            'id' => $this->sellerId,
            'ecuno' => $this->orderId,
            'receipt_no' => 10016,
            'eamount' => $this->amount * 100,
            'cur' => $this->currency,
            'respcode' => '000',
            'msgdata' => 'Test makse',
            'actiontext' => 'OK, tehing autoriseeritud',
            'mac' => '10e8d613d3d29f4f110ed7d624de85b436ea4b3bf11dcec46f77292f3ce494bf6d8c8f0600e17904b82289e8fa4eecfa65c4f3c015888abcb882ed5b362f3f46ef089912f3b12a89abe59683f6df9f1954723ce59e778e8d3838c71d1e78e48786e36b7619012f7aaa7390bfad24b008d09657779bfb0c283e6826a092928336',
            'datetime' => $this->expectedDate,
            'charEncoding' => 'UTF-8',
        );

        $response = $this->estcard->handleResponse($responseData);

        $this->assertInstanceOf('RKD\Banklink\Response\PaymentResponse', $response);
        $this->assertEquals(PaymentResponse::STATUS_SUCCESS, $response->getStatus());

        // This is valid response
        $this->assertTrue($response->wasSuccessful());
    }

    /**
     * Test custom request url.
     */
    public function testCustomRequestUrl()
    {
        $this->seb = new $this->bankClass($this->protocol);
        $this->seb->setRequestUrl('http://google.com');

        $request = $this->seb->getPaymentRequest($this->orderId, $this->amount, $this->message, $this->language, $this->currency, $this->timezone);

        // Get same data again, already exists
        $request = $this->seb->getPaymentRequest($this->orderId, $this->amount, $this->message, $this->language, $this->currency, $this->timezone);

        // Custom url
        $this->assertEquals('http://google.com', $request->getRequestUrl());
    }
}
