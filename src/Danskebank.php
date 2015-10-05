<?php
/**
 * RKD Banklink
 *
 * @package Banklink
 * @link https://github.com/renekorss/Banklink/
 * @author Rene Korss <rene.korss@gmail.com>
 * @copyright 2015 Rene Korss
 * @license MIT
 */

namespace RKD\Banklink;

use RKD\Banklink\Protocol\iPizza;

/**
 * Banklink settings for Danske bank
 *
 * For more information, please visit: https://www.danskebank.ee/public/documents/Pangalingi_tehniline_spetsifikatsioon_2014.pdf
 *
 * @author  Rene Korss <rene.korss@gmail.com>
 */

class Danskebank extends Banklink{

    /**
     * Request url
     * @var string
     */
    protected $requestUrl     = 'https://www2.danskebank.ee/ibank/pizza/pizza';

    /**
     * Test request url
     * @var string
     */
    protected $testRequestUrl = 'http://localhost:8080/banklink/sampo-common';

    /**
     * Force Danskebank class to use iPizza protocol
     *
     * @param RKD\Banklink\Protocol\iPizza $protocol Protocol used
     * @param boolean $debug Is in debug mode?
     * @param string $requestUrl Request URL
     */

    public function __construct(iPizza $protocol, $debug = false, $requestUrl = null){
        parent::__construct($protocol, $debug, $requestUrl);
    }

    /**
     * Danskebank uses UTF-8
     *
     * @return array Array of additional fields to send to bank
     */
    protected function getAdditionalFields(){
        return array();
    }
}
