<?php
namespace RKD\Banklink;

include_once 'SEBTest.php';

/**
 * Test suite for LHV banklink
 *
 * @author  Rene Korss <rene.korss@gmail.com>
 */

class LHVTest extends SEBTest{

    protected $bankClass      = "RKD\Banklink\LHV";

    protected $requestUrl     = "https://www.lhv.ee/banklink";
    protected $testRequestUrl = "http://localhost:8080/banklink/lhv-common";

}
