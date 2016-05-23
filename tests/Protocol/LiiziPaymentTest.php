<?php

namespace RKD\BanklinkTests\IPizza;

use RKD\Banklink\Protocol\LiiziPayment;
use RKD\BanklinkTests\IPizza\IPizzaTest;

/**
 * Test suite for Liizi payment protocol.
 *
 * @author  Rene Korss <rene.korss@gmail.com>
 */
class LiiziPaymentTest extends IPizzaTest
{

   /**
     * Set test data.
     */
    public function setUp()
    {
        parent::setUp();

        $this->protocol = new LiiziPayment(
            $this->sellerId,
            __DIR__.'/../keys/IPizza/private_key.pem',
            '',
            __DIR__.'/../keys/IPizza/public_key.pem',
            $this->requestUrl
        );
    }

    /**
     * Authentication should throw an LogicException
     *
     * @expectedException LogicException
     */
    public function testGetAuthRequest()
    {
        $request = $this->protocol->getAuthRequest();
    }
}
