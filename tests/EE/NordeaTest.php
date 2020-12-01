<?php

namespace RKD\Banklink\Test\EE;

/**
 * Test suite for Nordea banklink.
 *
 * @author  Rene Korss <rene.korss@gmail.com>
 */
class NordeaTest extends SEBTest
{
    protected $bankClass = "RKD\Banklink\EE\Nordea";

    protected $requestUrl = [
        'payment' => 'https://banklink.luminor.ee',
        'auth' => 'https://banklink.luminor.ee'
    ];
    protected $testRequestUrl = [
        'payment' => 'https://banklink.luminor.ee/test',
        'auth' => 'https://banklink.luminor.ee/test'
    ];
}
