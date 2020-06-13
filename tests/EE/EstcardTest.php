<?php

namespace RKD\Banklink\Test\EE;

use LogicException;
use PHPUnit\Framework\TestCase;
use RKD\Banklink;
use RKD\Banklink\Request\PaymentRequest;
use RKD\Banklink\Response\PaymentResponse;

/**
 * Test suite for Estcard payment gateway.
 *
 * @author  Rene Korss <rene.korss@gmail.com>
 */
class EstcardTest extends TestCase
{
    protected $bankClass = "RKD\Banklink\EE\Estcard";
    protected $protocolClass = "RKD\Banklink\Protocol\ECommerce";

    protected $requestUrl = 'https://pos.estcard.ee/ecom/iPayServlet';
    protected $testRequestUrl = 'https://test.estcard.ee/ecom/iPayServlet';

    /**
     * Set test data.
     */
    public function setUp() : void
    {
        $this->sellerId         = 'uid100081';
        $this->sellerName       = 'Ülo Pääsuke';
        $this->sellerAccount    = '1010342342354345435';

        $this->senderName       = 'Toomas Jäär';

        $this->orderId          = 201605861819;
        $this->amount           = 150;
        $this->message          = 'refnr:123;100:ABC123;';
        $this->language         = 'EST';
        $this->currency         = 'EUR';
        $this->timezone         = 'Europe/Tallinn';

        // From ENV variable
        $this->datetime         = getenv('TEST_DATETIME');

        $datetime               = new \Datetime($this->datetime, new \DateTimeZone($this->timezone));
        $this->expectedDate     = $datetime->format('YmdHis');

        $this->customRequestUrl = 'http://example.com';

        $this->expectedData = [
            'action'         => 'gaf',
            'ver'            => '004',
            'id'             => $this->sellerId,
            'ecuno'          => $this->orderId,
            'eamount'        => round($this->amount * 100, 2),
            'cur'            => $this->currency,
            'feedBackUrl'    => $this->customRequestUrl,
            'lang'           => 'et',
            'delivery'       => 'S',
            'mac'            => '460cdd346cc2f919bd9d1572bb030a83075706dd65533d2a60a1d5e4fba0292c7f450efea3f6a070a76fdb91cc34c9d71341410336b04c4fb60ea5930f9b1b644e4a77e49927e85c2d24062bf197a1d7e60aa0b562f0768baeda101f2b497e0a19cca5100b22db2bb4244ca6bf03471e56f816e0beee241cdf3b7fed558ddc41',
            'datetime'       => $this->expectedDate,
            'charEncoding'   => 'UTF-8',
            'additionalinfo' => 'refnr:123;100:ABC123;'
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
            __DIR__.'/../keys/IPizza/private_key.pem',
            '',
            __DIR__.'/../keys/IPizza/public_key.pem',
            $this->customRequestUrl
        );

        $this->bank = new $this->bankClass($this->protocol);
    }

    /**
     * Test for correctly generated request data.
     */
    public function testGetPaymentRequest()
    {

        // Test service 1012
        $request = $this->bank->getPaymentRequest($this->orderId, $this->amount, $this->message, $this->language, $this->currency, [], $this->timezone);

        // Instance of PaymentRequest and data is same
        $this->assertInstanceOf('RKD\Banklink\Request\PaymentRequest', $request);
        $this->assertEquals($this->expectedData, $request->getRequestData());

        // Production env url
        $this->assertEquals($this->requestUrl, $request->getRequestUrl());
    }

    /**
     * Test for correctly generated request data.
     */
    public function testGetPaymentRequestWithoutMessage()
    {
        $request = $this->bank->getPaymentRequest($this->orderId, $this->amount, '', $this->language, $this->currency, [], $this->timezone);

        unset($this->expectedData['additionalinfo']);
        $this->expectedData['mac'] = 'aaacb942dd3512d915224d244c20862457284e72587057d182ee1ee1b6da1082b43632cf9a9138144f52b48edc6fe8cdeb2193320f7a651c670c3550c92ae619c8fd33713f313d8c88241ec8322c78831bb818715eee3584ed612891ea4ce7a31398d280aa7b878907a7f6a2915629a4d369ddd2b1c0b56ad8dec19f5fafb35f';
        $this->assertEquals($this->expectedData, $request->getRequestData());
    }

    /**
      * Test successful payment response.
      */
    public function testHandlePaymentResponseSuccess()
    {
        $responseData = [
            'action'       => 'afb',
            'ver'          => '4',
            'id'           => $this->sellerId,
            'ecuno'        => $this->orderId,
            'receipt_no'   => 10016,
            'eamount'      => $this->amount * 100,
            'cur'          => $this->currency,
            'respcode'     => '000',
            'msgdata'      => 'Test makse',
            'actiontext'   => 'OK, tehing autoriseeritud',
            'mac'          => '10e8d613d3d29f4f110ed7d624de85b436ea4b3bf11dcec46f77292f3ce494bf6d8c8f0600e17904b82289e8fa4eecfa65c4f3c015888abcb882ed5b362f3f46ef089912f3b12a89abe59683f6df9f1954723ce59e778e8d3838c71d1e78e48786e36b7619012f7aaa7390bfad24b008d09657779bfb0c283e6826a092928336',
            'datetime'     => $this->expectedDate,
            'charEncoding' => 'UTF-8',
            'feedBackUrl'  => '',
            'auto'         => 'N',
        ];

        $response = $this->bank->handleResponse($responseData);

        $this->assertInstanceOf('RKD\Banklink\Response\PaymentResponse', $response);
        $this->assertEquals(PaymentResponse::STATUS_SUCCESS, $response->getStatus());

        // We should get same message
        $this->assertEquals('Test makse', $response->getMessage());

        // This is valid response
        $this->assertTrue($response->wasSuccessful());
    }

    /**
     * Authentication should throw an LogicException
     */
    public function testGetAuthRequest4011()
    {
        $this->expectException(LogicException::class);

        $this->bank->getAuthRequest();
    }

    /**
     * Authentication should throw an LogicException
     */
    public function testGetAuthRequest4012()
    {
        $this->expectException(LogicException::class);

        $this->bank->getAuthRequest('bank-id', 'random-nonce', 'random-rid');
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
        $this->assertEquals('https://google.com', $request->getRequestUrl());
    }
}
