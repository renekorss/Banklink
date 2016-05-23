<?php

namespace RKD\BanklinkTests;

use RKD\Banklink\Protocol\LiiziPayment;

/**
 * Test suite for Liizi banklink.
 *
 * @author  Rene Korss <rene.korss@gmail.com>
 */
class LiiziTest extends SEBTest
{
    protected $bankClass = "RKD\Banklink\Liizi";
    protected $protocolClass = "RKD\Banklink\Protocol\LiiziPayment";

    protected $requestUrl = 'https://klient.liisi.ee/api/ipizza/';
    protected $testRequestUrl = 'https://prelive.liisi.ee:8953/api/ipizza/';

    /**
     * Authentication should throw an LogicException
     *
     * @expectedException LogicException
     */
    public function testGetAuthRequest4011()
    {
        $request = $this->bank->getAuthRequest();
    }

    /**
     * Authentication should throw an LogicException
     *
     * @expectedException LogicException
     */
    public function testGetAuthRequest4012()
    {
        $request = $this->bank->getAuthRequest('bank-id', 'random-nonce', 'random-rid');
    }
}
