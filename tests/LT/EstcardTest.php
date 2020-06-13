<?php

namespace RKD\Banklink\Test\LT;

use RKD\Banklink\Test\EE\EstcardTest as EstcardTestBase;

/**
 * Test suite for Estcard payment gateway.
 *
 * @author  Rene Korss <rene.korss@gmail.com>
 */
class EstcardTest extends EstcardTestBase
{
    protected $bankClass = "RKD\Banklink\LT\Estcard";
    protected $protocolClass = "RKD\Banklink\Protocol\ECommerce";
}
