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
 * Banklink settings for Nordea.
 *
 * For more information, please visit:
 * http://www.nordea.ee/Corporate%20customers/Daily%20banking/Collection%20of%20payment/E-payment/798312.html
 *
 * @author Rene Korss <rene.korss@gmail.com>
 */

class Nordea extends Banklink
{
    /**
     * Request url.
     *
     * @var mixed
     */
    protected $requestUrl = 'https://banklink.luminor.ee';

    /**
     * Test request url.
     *
     * @var mixed
     */
    protected $testRequestUrl = 'https://banklink.luminor.ee/test';

    /**
     * Force Nordea class to use IPizza protocol.
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
