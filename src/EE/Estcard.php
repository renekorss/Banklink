<?php
/**
 * RKD Banklink.
 *
 * @link https://github.com/renekorss/Banklink/
 *
 * @author Rene Korss <rene.korss@gmail.com>
 * @copyright 2016-2020 Rene Korss
 * @license MIT
 */
namespace RKD\Banklink\EE;

use RKD\Banklink\Banklink;
use RKD\Banklink\Protocol\ECommerce;

/**
 * Banklink settings for Estcard.
 *
 * For more information, please visit:
 * http://www.estcard.ee/publicweb/files/ecomdevel/e-comDocENG.html
 *
 * @author Rene Korss <rene.korss@gmail.com>
 */

class Estcard extends Banklink
{
    /**
     * Request url.
     *
     * @var mixed
     */
    protected $requestUrl = 'https://pos.estcard.ee/ecom/iPayServlet';

    /**
     * Test request url.
     *
     * @var mixed
     */
    protected $testRequestUrl = 'https://test.estcard.ee/ecom/iPayServlet';

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
    protected function getEncodingField() : string
    {
        return 'charEncoding';
    }

    /**
     * By default uses UTF-8.
     *
     * @return array Array of additional fields to send to bank
     */
    protected function getAdditionalFields() : array
    {
        return [
            'charEncoding' => $this->requestEncoding,
        ];
    }
}
