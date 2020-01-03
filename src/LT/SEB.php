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
use RKD\Banklink\Protocol\IPizza2015;

/**
 * Banklink settings for SEB.
 *
 * For more information, please visit:
 * https://www.seb.lt/eng/business/online-banking/online-services/e-commerce-bank-link-service
 *
 * @author Rene Korss <rene.korss@gmail.com>
 */
class SEB extends Banklink
{
    /**
     * Request url.
     *
     * @var mixed
     */
    protected $requestUrl = 'https://e.seb.lt/mainib/web.p';

    /**
     * Test request url.
     *
     * @var mixed
     */
    protected $testRequestUrl = 'https://e.seb.lt/mainib/web.p';

    /**
     * Force SEB class to use IPizza2015 protocol.
     *
     * @param RKD\Banklink\Protocol\IPizza2015 $protocol   Protocol used
     */
    public function __construct(IPizza2015 $protocol)
    {
        parent::__construct($protocol);
    }
}
