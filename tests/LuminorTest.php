<?php

namespace RKD\BanklinkTests;

/**
 * Test suite for Luminor banklink.
 *
 * @author  Rene Korss <rene.korss@gmail.com>
 */
class LuminorTest extends SEBTest
{
    protected $bankClass = "RKD\Banklink\Luminor";

    protected $requestUrl = [
        'payment' => 'https://netbank.nordea.com/pnbepay/epayp.jsp',
        'auth' => 'https://netbank.nordea.com/pnbepay/epayp.jsp'
    ];
    protected $testRequestUrl = [
        'payment' => 'https://netbank.nordea.com/pnbepaytest/epayp.jsp',
        'auth' => 'https://netbank.nordea.com/pnbepaytest/epayp.jsp'
    ];
}
