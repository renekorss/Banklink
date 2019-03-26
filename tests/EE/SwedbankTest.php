<?php

namespace RKD\Banklink\Test\EE;

/**
 * Test suite for Swedbank banklink.
 *
 * @author  Rene Korss <rene.korss@gmail.com>
 */
class SwedbankTest extends SEBTest
{
    protected $bankClass = "RKD\Banklink\EE\Swedbank";

    protected $requestUrl = [
        'payment' => 'https://www.swedbank.ee/banklink',
        'auth' => 'https://www.swedbank.ee/banklink'
    ];
    protected $testRequestUrl = [
        'payment' => 'https://www.swedbank.ee/banklink',
        'auth' => 'https://www.swedbank.ee/banklink'
    ];
}
