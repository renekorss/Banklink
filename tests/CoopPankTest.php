<?php

namespace RKD\BanklinkTests;

/**
 * Test suite for Coop Pank banklink.
 *
 * @author  Rene Korss <rene.korss@gmail.com>
 */
class CoopPankTest extends SEBTest
{
    protected $bankClass = "RKD\Banklink\CoopPank";

    protected $requestUrl = 'https://i-pank.krediidipank.ee/teller/maksa';
    protected $testRequestUrl = 'http://localhost:8080/banklink/krediidipank-common';
}
