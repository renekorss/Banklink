<?php

namespace RKD\Banklink\Test\EE;

/**
 * Test suite for LHV banklink.
 *
 * @author  Rene Korss <rene.korss@gmail.com>
 */
class PocopayTest extends SEBTest
{
    protected $bankClass = "RKD\Banklink\EE\Pocopay";

    protected $requestUrl = [
        'payment' => 'https://my.pocopay.com/banklink',
        'auth' => 'https://my.pocopay.com/banklink'
    ];
    protected $testRequestUrl = [
        'payment' => 'https://alpha.elementare.eu/banklink',
        'auth' => 'https://alpha.elementare.eu/banklink'
    ];
}
