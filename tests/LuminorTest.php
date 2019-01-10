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

    protected $requestUrl = 'https://netbank.nordea.com/pnbepay/epayp.jsp';
    protected $testRequestUrl = 'https://netbank.nordea.com/pnbepaytest/epayp.jsp';
}
