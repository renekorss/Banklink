<?php

namespace RKD\BanklinkTests;

/**
 * Test suite for LHV banklink.
 *
 * @author  Rene Korss <rene.korss@gmail.com>
 */
class PocopayTest extends SEBTest
{
    protected $bankClass = "RKD\Banklink\Pocopay";

    protected $requestUrl = 'https://my.pocopay.com/banklink';
    protected $testRequestUrl = 'http://localhost:8080/banklink/seb-common';
}
