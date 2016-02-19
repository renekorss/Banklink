<?php

namespace RKD\BanklinkTests;

/**
 * Test suite for Nordea banklink.
 *
 * @author  Rene Korss <rene.korss@gmail.com>
 */
class NordeaTest extends SEBTest
{
    protected $bankClass = "RKD\Banklink\Nordea";

    protected $requestUrl = 'https://netbank.nordea.com/pnbepay/epayp.jsp';
    protected $testRequestUrl = 'http://localhost:8080/banklink/ipizza';
}
