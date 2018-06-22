<?php

namespace RKD\BanklinkTests\Protocol\Helper;

use RKD\Banklink\Protocol\Helper\ProtocolHelper;
use PHPUnit\Framework\TestCase;

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
     * Test exception for too long order id.
     *
     * @expectedException InvalidArgumentException
     */
    public function testReferenceTooLong()
    {
        $orderId = '12345678901234567890';
        ProtocolHelper::calculateReference($orderId);
    }

    /**
     * Test exception for too short order id.
     *
     * @expectedException InvalidArgumentException
     */
    public function testReferenceTooShortNotInteger()
    {
        ProtocolHelper::calculateReference('');
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
