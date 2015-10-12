<?php
namespace RKD\Banklink;

include_once 'SEBTest.php';

/**
 * Test suite for Krediidipank banklink
 *
 * @author  Rene Korss <rene.korss@gmail.com>
 */

class KrediidipankTest extends SEBTest{

    protected $bankClass      = "RKD\Banklink\Krediidipank";

    protected $requestUrl     = "https://i-pank.krediidipank.ee/teller/maksa";
    protected $testRequestUrl = "http://localhost:8080/banklink/krediidipank-common";

}
