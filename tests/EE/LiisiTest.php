<?php

namespace RKD\Banklink\Test\EE;

use LogicException;

/**
 * Test suite for Liisi banklink.
 *
 * @author  Rene Korss <rene.korss@gmail.com>
 *
 * @SuppressWarnings(PHPMD.TooManyFields)
 * @SuppressWarnings(PHPMD.TooManyPublicMethods)
 */
class LiisiTest extends SEBTest
{
    protected $bankClass = "RKD\Banklink\EE\Liisi";
    protected $protocolClass = "RKD\Banklink\Protocol\LiisiPayment";

    protected $requestUrl = [
        'payment' => 'https://klient.liisi.ee/api/ipizza/',
        'auth' => 'https://klient.liisi.ee/api/ipizza/'
    ];
    protected $testRequestUrl = [
        'payment' => 'https://prelive.liisi.ee/api/ipizza/',
        'auth' => 'https://prelive.liisi.ee/api/ipizza/'
    ];

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
}
