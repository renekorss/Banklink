<?php

namespace RKD\Banklink\Test\Protocol;

use RKD\Banklink\Protocol\LiisiPayment;

/**
 * Test suite for Liisi payment protocol.
 *
 * @author  Rene Korss <rene.korss@gmail.com>
 */
class LiisiPaymentTest extends IPizzaTest
{

   /**
     * Set test data.
     */
    public function setUp()
    {
        parent::setUp();

        $this->protocol = new LiisiPayment(
            $this->sellerId,
            __DIR__.'/../keys/IPizza/private_key.pem',
            '',
            __DIR__.'/../keys/IPizza/public_key.pem',
            $this->requestUrl
        );
    }

    /**
     * Test successful authentication response.
     *
     * @group ignore
     */
    public function testHandleAuthResponseSuccess()
    {
        // Auth not supported
    }

    /**
     * Test failed authentication response.
     *
     * @group ignore
     */
    public function testHandleAuthResponseError()
    {
        // Auth not supported
    }

    /**
     * Test authentication request data
     *
     * @expectedException LogicException
     */
    public function testGetAuthRequest4011()
    {
        parent::testGetAuthRequest4011();
    }

    /**
     * Test authentication request data
     *
     * @expectedException LogicException
     */
    public function testGetAuthRequest4012()
    {
        parent::testGetAuthRequest4012();
    }
}
