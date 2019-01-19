<?php

namespace RKD\BanklinkTests;

/**
 * Test suite for LHV banklink.
 *
 * @author  Rene Korss <rene.korss@gmail.com>
 */
class LHVTest extends SEBTest
{
    protected $bankClass = "RKD\Banklink\LHV";

    protected $requestUrl = [
        'payment' => 'https://www.lhv.ee/banklink',
        'auth' => 'https://www.lhv.ee/banklink'
    ];
    protected $testRequestUrl = [
        'payment' => 'https://www.lhv.ee/banklink',
        'auth' => 'https://www.lhv.ee/banklink'
    ];
}
