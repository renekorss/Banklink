<?php

namespace RKD\Banklink\Test\EE;

/**
 * Test suite for Danskebank banklink.
 *
 * @author  Rene Korss <rene.korss@gmail.com>
 */
class DanskebankTest extends SEBTest
{
    protected $bankClass = "RKD\Banklink\EE\Danskebank";

    protected $requestUrl = [
        'payment' => 'https://e.danskebank.ee/ib/site/ibpay/login',
        'auth' => 'https://e.danskebank.ee/ib/site/ibpay/login'
    ];
    protected $testRequestUrl = [
        'payment' => 'https://e.danskebank.ee/ib/site/ibpay/login',
        'auth' => 'https://e.danskebank.ee/ib/site/ibpay/login'
    ];
}
