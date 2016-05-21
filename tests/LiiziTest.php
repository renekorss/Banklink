<?php

namespace RKD\BanklinkTests;

/**
 * Test suite for Liizi banklink.
 *
 * @author  Rene Korss <rene.korss@gmail.com>
 */
class LiiziTest extends SEBTest
{
    protected $bankClass = "RKD\Banklink\Liizi";

    protected $requestUrl = 'https://klient.liisi.ee/api/ipizza/';
    protected $testRequestUrl = 'https://prelive.liisi.ee:8953/api/ipizza/';
}
