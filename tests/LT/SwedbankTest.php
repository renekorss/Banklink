<?php

namespace RKD\Banklink\Test\LT;

/**
 * Test suite for Swedbank banklink.
 *
 * @author  Rene Korss <rene.korss@gmail.com>
 */
class SwedbankTest extends SEBTest
{
    protected $bankClass = "RKD\Banklink\LT\Swedbank";

    protected $requestUrl = [
        'payment' => 'https://www.swedbank.lt/banklink/',
        'auth' => 'https://www.swedbank.lt/banklink/'
    ];
    protected $testRequestUrl = [
        'payment' => 'https://www.swedbank.lt/banklink/',
        'auth' => 'https://www.swedbank.lt/banklink/'
    ];

    /**
     * Set test data.
     */
    public function setUp()
    {
        parent::setUp();

        // Swedbank has encoding
        $this->expectedData['VK_ENCODING'] = 'UTF-8';

        $this->setUpBanklinkWithSeller();
    }
}
