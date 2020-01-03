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
use RKD\Banklink\Protocol\LiisiPayment;

/**
 * Settings for Liisi payment link.
 *
 * For more information, please visit:
 * https://klient.liisi.ee/static/payment_link_doc/
 *
 * @author Rene Korss <rene.korss@gmail.com>
 */

class Liisi extends Banklink
{
    /**
     * Request url.
     *
     * @var mixed
     */
    protected $requestUrl = 'https://klient.liisi.ee/api/ipizza/';

    /**
     * Test request url.
     *
     * @var mixed
     */
    protected $testRequestUrl = 'https://prelive.liisi.ee:8953/api/ipizza/';

    /**
     * Force Liisi class to use LiisiPayment protocol.
     *
     * @param RKD\Banklink\Protocol\LiisiPayment $protocol   Protocol used
     */
    public function __construct(LiisiPayment $protocol)
    {
        parent::__construct($protocol);
    }

    /**
     * Override encoding field.
     */
    protected function getEncodingField() : string
    {
        return 'VK_ENCODING';
    }

    /**
     * By default uses UTF-8.
     *
     * @return array Array of additional fields to send to bank
     */
    protected function getAdditionalFields() : array
    {
        return [
            'VK_ENCODING' => $this->requestEncoding,
        ];
    }
}
