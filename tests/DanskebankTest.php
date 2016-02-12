<?php

namespace RKD\BanklinkTests;

/**
 * Test suite for Danskebank banklink.
 *
 * @author  Rene Korss <rene.korss@gmail.com>
 */
class DanskebankTest extends SEBTest
{
    protected $bankClass = "RKD\Banklink\Danskebank";

    protected $requestUrl = 'https://www2.danskebank.ee/ibank/pizza/pizza';
    protected $testRequestUrl = 'http://localhost:8080/banklink/sampo-common';
}
