<?php

namespace RKD\Banklink\Test\Protocol\Helper;

use PHPUnit\Framework\TestCase;
use RKD\Banklink\Protocol\Helper\ProtocolHelper;
use TypeError;

/**
 * Test suite for protocol helper.
 *
 * @author  Rene Korss <rene.korss@gmail.com>
 */
class ProtocolHelperTest extends TestCase
{
    /**
     * Test reference calculator.
     */
    public function testcalculateReference()
    {
        $orderId = 12131295;
        $expectedReference = 121312952;

        $this->assertEquals($expectedReference, ProtocolHelper::calculateReference($orderId));

        $orderId = 12131495;
        $expectedReference = 121314950;

        $this->assertEquals($expectedReference, ProtocolHelper::calculateReference($orderId));
    }

    /**
     * Test exception for too short order id.
     */
    public function testReferenceTooShortNotInteger()
    {
        $this->expectException(TypeError::class);

        ProtocolHelper::calculateReference('randomstring');
    }

    /**
     * Test language code converter
     */
    public function testLangToISO6391()
    {
        $this->assertEquals('et', ProtocolHelper::langToISO6391('est'));
        $this->assertEquals('ru', ProtocolHelper::langToISO6391('rus'));
        $this->assertEquals('en', ProtocolHelper::langToISO6391('eng'));
        $this->assertEquals('fi', ProtocolHelper::langToISO6391('fin'));
    }

    /**
     * Test ecuno generator
     */
    public function testGenerateEcuno()
    {
        $ecuno = ProtocolHelper::generateEcuno();

        $this->assertStringStartsWith(date('Ym'), $ecuno);
        $this->assertEquals(12, strlen($ecuno));
    }
}
