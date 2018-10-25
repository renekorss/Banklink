<?php

namespace RKD\BanklinkTests\ECommerce;

use RKD\Banklink\Protocol\ECommerce;
use PHPUnit\Framework\TestCase;

/**
 * Test suite for ECommerce protocol.
 *
 * @author  Rene Korss <rene.korss@gmail.com>
 */
class ECommerceTest extends TestCase
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

    protected $requestUrl;

    /**
     * Set test data.
     */
    public function setUp()
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

        $this->protocol = new ECommerce(
            $this->sellerId,
            __DIR__.'/../keys/IPizza/private_key.pem',
            '',
            __DIR__.'/../keys/IPizza/public_key.pem',
            $this->requestUrl
        );
    }

    /**
     * @expectedException UnexpectedValueException
     */
    public function testGetRequestFieldMissing()
    {
        $this->protocol->getPaymentRequest(null, $this->amount, $this->message, $this->language, $this->currency, [], 'UTF-8', $this->timezone);
    }

    /**
     * Test invalid public key.
     *
     * @expectedException UnexpectedValueException
     */
    public function testInvalidPublicKey()
    {
        $this->protocol = new ECommerce(
            $this->sellerId,
            __DIR__.'/../keys/IPizza/private_key.pem',
            '',
            'no-key',
            $this->requestUrl,
            $this->sellerName,
            $this->sellerAccount
        );

        $responseData = array(
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
            'datetime'     => '2015-10-12T08:47:15+0300',
            'charEncoding' => 'UTF-8',
        );

        $this->protocol->handleResponse($responseData);
    }

    /**
     * Test invalid private key.
     *
     * @expectedException UnexpectedValueException
     */
    public function testInvalidPrivateKey()
    {
        $this->protocol = new ECommerce(
            $this->sellerId,
            'no-key',
            '',
            __DIR__.'/../keys/IPizza/public_key.pem',
            $this->requestUrl,
            $this->sellerName,
            $this->sellerAccount
        );

        $this->protocol->getPaymentRequest($this->orderId, $this->amount, $this->message);
    }

    /**
     * Authentication should throw an LogicException
     *
     * @expectedException LogicException
     */
    public function testGetAuthRequest()
    {
        $this->protocol->getAuthRequest();
    }
}
