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

    protected $requestUrl = 'https://i.cooppank.ee/auth';
    protected $testRequestUrl = 'https://i.cooppank.ee/auth';
}
