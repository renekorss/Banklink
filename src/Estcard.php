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

use RKD\Banklink\Protocol\ECommerce;

/**
 * Banklink settings for Estcard.
 *
 * For more information, please visit:
 * http://www.estcard.ee/publicweb/files/ecomdevel/e-comDocENG.html
 *
 * @author  Rene Korss <rene.korss@gmail.com>
 */

class Estcard extends Banklink
{
    /**
     * Request url.
     *
     * @var string
     */
    protected $requestUrl = 'https://pos.estcard.ee/test-pos/iPayServlet';

    /**
     * Test request url.
     *
     * @var string
     */
    protected $testRequestUrl = 'http://localhost:8080/banklink/ec';

    /**
     * Force Nordea class to use ECommerce protocol.
     *
     * @param RKD\Banklink\Protocol\ECommerce $protocol   Protocol used
     */
    public function __construct(ECommerce $protocol)
    {
        parent::__construct($protocol);
    }

    /**
     * Override encoding field.
     */
    protected function getEncodingField()
    {
        return 'charEncoding';
    }

    /**
     * Swedbank uses UTF-8.
     *
     * @return array Array of additional fields to send to bank
     */
    protected function getAdditionalFields()
    {
        return array(
            'charEncoding' => $this->requestEncoding,
        );
    }
}
