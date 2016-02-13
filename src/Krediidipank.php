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
 * Banklink settings for Krediidipank.
 *
 * For more information, please visit: http://www.krediidipank.ee/business/settlements/bank-link/index.html
 *
 * @author  Rene Korss <rene.korss@gmail.com>
 */
class Krediidipank extends Banklink
{
    /**
     * Request url.
     *
     * @var string
     */
    protected $requestUrl = 'https://i-pank.krediidipank.ee/teller/maksa';

    /**
     * Test request url.
     *
     * @var string
     */
    protected $testRequestUrl = 'http://localhost:8080/banklink/krediidipank-common';

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
    protected function getEncodingField()
    {
        return 'VK_ENCODING';
    }

    /**
     * Krediidipank uses UTF-8.
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
