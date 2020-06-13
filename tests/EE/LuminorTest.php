<?php

namespace RKD\Banklink\Test\EE;

/**
 * Test suite for Luminor banklink.
 *
 * @author  Rene Korss <rene.korss@gmail.com>
 */
class LuminorTest extends SEBTest
{
    protected $bankClass = "RKD\Banklink\EE\Luminor";

    protected $requestUrl = [
        'payment' => 'https://netbank.nordea.com/pnbepay/epayp.jsp',
        'auth' => 'https://netbank.nordea.com/pnbeid/eidp.jsp'
    ];
    protected $testRequestUrl = [
        'payment' => 'https://netbank.nordea.com/pnbepaytest/epayp.jsp',
        'auth' => 'https://netbank.nordea.com/pnbeidtest/eidp.jsp'
    ];
}
