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
 * Banklink settings for Danske bank.
 *
 * For more information, please visit:
 * https://www.danskebank.ee/public/documents/Pangalingi_tehniline_spetsifikatsioon_2014.pdf
 *
 * @author  Rene Korss <rene.korss@gmail.com>
 */
class Danskebank extends Banklink
{
    /**
     * Request url.
     *
     * @var string
     */
    protected $requestUrl = 'https://www2.danskebank.ee/ibank/pizza/pizza';

    /**
     * Test request url.
     *
     * @var string
     */
    protected $testRequestUrl = 'http://localhost:8080/banklink/sampo-common';

    /**
     * Force Danskebank class to use IPizza protocol.
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
     * Danskebank uses UTF-8.
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
