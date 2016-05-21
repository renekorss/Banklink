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

use RKD\Banklink\Protocol\IPizza;

/**
 * Ssettings for Liizi payment link.
 *
 * For more information, please visit:
 * https://klient.liisi.ee/static/payment_link_doc/
 *
 * @author  Rene Korss <rene.korss@gmail.com>
 */

class Liizi extends Banklink
{
    /**
     * Request url.
     *
     * @var string
     */
    protected $requestUrl = 'https://klient.liisi.ee/api/ipizza/';

    /**
     * Test request url.
     *
     * @var string
     */
    protected $testRequestUrl = 'https://prelive.liisi.ee:8953/api/ipizza/';

    /**
     * Force Liizi class to use IPizza protocol.
     *
     * @param RKD\Banklink\Protocol\IPizza $protocol   Protocol used
     */
    public function __construct(IPizza $protocol)
    {
        parent::__construct($protocol);
    }

    /**
     * Override encoding field.
     */
    protected function getEncodingField()
    {
        return 'VK_ENCODING';
    }

    /**
     * Liizi uses UTF-8.
     *
     * @return array Array of additional fields to send to bank
     */
    protected function getAdditionalFields()
    {
        return array(
            'VK_ENCODING' => $this->requestEncoding,
        );
    }
}
