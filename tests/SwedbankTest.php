<?php
namespace RKD\Banklink;

include_once 'SEBTest.php';

/**
 * Test suite for Swedbank banklink
 *
 * @author  Rene Korss <rene.korss@gmail.com>
 */

class SwedbankTest extends SEBTest{

    protected $bankClass      = "RKD\Banklink\Swedbank";

    protected $requestUrl     = "https://www.swedbank.ee/banklink";
    protected $testRequestUrl = "http://localhost:8080/banklink/swedbank-common";

}
