<?php

namespace RKD\Banklink\Test\EE;

/**
 * Test suite for LHV banklink.
 *
 * @author  Rene Korss <rene.korss@gmail.com>
 */
class LHVTest extends SEBTest
{
    protected $bankClass = "RKD\Banklink\EE\LHV";

    protected $requestUrl = [
        'payment' => 'https://www.lhv.ee/banklink',
        'auth' => 'https://www.lhv.ee/banklink'
    ];
    protected $testRequestUrl = [
        'payment' => 'https://www.lhv.ee/banklink',
        'auth' => 'https://www.lhv.ee/banklink'
    ];
}
