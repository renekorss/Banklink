<?php
/**
 * RKD Banklink.
 *
 * @link https://github.com/renekorss/Banklink/
 *
 * @author Rene Korss <rene.korss@gmail.com>
 * @copyright 2016 Rene Korss
 * @license MIT
 */
namespace RKD\Banklink;

// use RKD\Banklink\Protocol\Solo;

/**
 * Banklink settings for Nordea.
 *
 * For more information, please visit:
 * http://www.nordea.ee/corporate+customers/daily+banking/collection+of+payment/e-payment/798312.html
 *
 * Coverage ignore, since SOLO protocol is not supported yet
 *
 * @author  Rene Korss <rene.korss@gmail.com>
 */
// @codeCoverageIgnoreStart
class Nordea extends Banklink
{
    /**
     * Request url.
     *
     * @var string
     */
    protected $requestUrl = 'https://netbank.nordea.com/pnbepay/epayn.jsp';

    /**
     * Test request url.
     *
     * @var string
     */
    protected $testRequestUrl = 'http://localhost:8080/banklink/nordea';

    /**
     * Force Nordea class to use Solo protocol.
     *
     * @param RKD\Banklink\Protocol\Solo $protocol   Protocol used
     */
    public function __construct(Solo $protocol)
    {
        // TODO
        // Must add support for SOLO protocol

        parent::__construct($protocol);
    }
}
// @codeCoverageIgnoreEnd
