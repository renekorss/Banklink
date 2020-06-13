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
 * Banklink settings for Coop Pank.
 *
 * For more information, please visit: https://www.cooppank.ee/en/bank-link
 *
 * @author Rene Korss <rene.korss@gmail.com>
 */
class CoopPank extends Banklink
{
    /**
     * Request url.
     *
     * @var mixed
     */
    protected $requestUrl = [
        'payment' => 'https://i.cooppank.ee/pay',
        'auth' => 'https://i.cooppank.ee/auth'
    ];

    /**
     * Test request url.
     *
     * @var mixed
     */
    protected $testRequestUrl = [
        'payment' => 'https://secure.cooppank.ee/pay',
        'auth' => 'https://secure.cooppank.ee/auth'
    ];

    /**
     * Response encoding.
     *
     * @var string
     */
    protected $responseEncoding = 'ISO-8859-13';

    /**
     * Force Krediidipank class to use IPizza protocol.
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
