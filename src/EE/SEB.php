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
use RKD\Banklink\Protocol\IPizza;

/**
 * Banklink settings for SEB.
 *
 * For more information, please visit:
 * http://www.seb.ee/eng/business/everyday-banking/collection-payments/collection-payments-internet
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
    protected $requestUrl = 'https://www.seb.ee/cgi-bin/unet3.sh/ipank.r';

    /**
     * Test request url.
     *
     * @var mixed
     */
    protected $testRequestUrl = 'https://e.seb.ee/cgi-bin/dv.sh/ipank.r';

    /**
     * Force SEB class to use IPizza protocol.
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
