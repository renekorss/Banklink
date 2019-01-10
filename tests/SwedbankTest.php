<?php

namespace RKD\BanklinkTests;

/**
 * Test suite for Swedbank banklink.
 *
 * @author  Rene Korss <rene.korss@gmail.com>
 */
class SwedbankTest extends SEBTest
{
    protected $bankClass = "RKD\Banklink\Swedbank";

    protected $requestUrl = 'https://www.swedbank.ee/banklink';
    protected $testRequestUrl = 'https://www.swedbank.ee/banklink';
}
