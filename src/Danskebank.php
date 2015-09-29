<?php
namespace RKD\Banklink;

use RKD\Banklink\Protocol\iPizza;

/**
 * Banklink settings for Danskebank
 *
 * For more information, please visit: https://www.danskebank.ee/public/documents/Pangalingi_tehniline_spetsifikatsioon_2014.pdf
 *
 * @author  Rene Korss <rene.korss@gmail.com>
 */

class Danskebank extends Banklink{

    protected $requestUrl     = 'https://www2.danskebank.ee/ibank/pizza/pizza';
    protected $testRequestUrl = 'http://localhost:8080/banklink/sampo-common';

    /**
     * Force Danskebank class to use iPizza protocol
     *
     * @param RKD\Banklink\Protocol\iPizza $protocol
     * @param boolean Is in debug mode?
     * @param string Response URL
     *
     * @return void
     */

    public function __construct(iPizza $protocol, $debug = false, $requestUrl = null){
        parent::__construct($protocol, $debug, $requestUrl);
    }

    /**
     * Danskebank uses UTF-8
     *
     * @return array Array of additiona fields to send to bank
     */
    protected function getAdditionalFields(){
        return array();
    }
}
