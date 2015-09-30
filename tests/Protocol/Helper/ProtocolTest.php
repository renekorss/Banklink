<?php
namespace RKD\Banklink\Protocol\Helper;

use RKD\Banklink\Protocol\Helper\ProtocolHelper;

/**
 * Test suite for protocol helper
 *
 * @author  Rene Korss <rene.korss@gmail.com>
 */

class ProtocolHelperTest extends \PHPUnit_Framework_TestCase{

    /**
     * Test reference calculator
     */

    public function testcalculateReference(){

        $orderId           = 12131295;
        $expectedReference = 121312952;

        $this->assertEquals($expectedReference, ProtocolHelper::calculateReference($orderId));

        $orderId           = 12131495;
        $expectedReference = 121314950;

        $this->assertEquals($expectedReference, ProtocolHelper::calculateReference($orderId));
    }

    /**
     * Test exception for too long order id
     *
     * @expectedException InvalidArgumentException
     */

    public function testReferenceTooLong(){

        $orderId = '12345678901234567890';
        ProtocolHelper::calculateReference($orderId);
    }

    /**
     * Test exception for too short order id
     *
     * @expectedException InvalidArgumentException
     */

    public function testReferenceTooShortNotInteger(){
        ProtocolHelper::calculateReference('');
    }

}
