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
namespace RKD\Banklink\LT;

use RKD\Banklink\Banklink;
use RKD\Banklink\Protocol\IPizza\Services2015;
use RKD\Banklink\Protocol\IPizza2015;

/**
 * Banklink settings for Luminor.
 *
 * For more information, please visit:
 * https://www.luminor.lt/en/business/e-commerce-banklink
 *
 * @author Rene Korss <rene.korss@gmail.com>
 */

class Luminor extends Banklink
{
    /**
     * Request url.
     *
     * @var mixed
     */
    protected $requestUrl = 'https://ib.dnb.lt/loginb2b.aspx';

    /**
     * Test request url.
     *
     * @var mixed
     */
    protected $testRequestUrl = 'https://ib.dnb.lt/loginb2b.aspx';

    /**
     * Force Luminor class to use IPizza2015 protocol.
     *
     * @param RKD\Banklink\Protocol\IPizza2015 $protocol   Protocol used
     */
    public function __construct(IPizza2015 $protocol)
    {
        /**
         * Luminor LT uses 2001 as payment service
         */
        $protocol->setServiceId(Services2015::PAYMENT_REQUEST_2001);

        parent::__construct($protocol);
    }

    /**
     * Additional request fields merged to request data
     *
     * @return array Array of additional request fields to send to bank
     */
    protected function getAdditionalRequestFields() : array
    {
        return [
            'VK_PANK' => '40100'
        ];
    }
}
