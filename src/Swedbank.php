<?php
namespace RKD\Banklink;

use RKD\Banklink\Protocol\iPizza;

/**
 * Banklink settings for Swedbank
 *
 * For more information, please visit: https://www.swedbank.ee/business/cash/ecommerce/banklink
 *
 * @author  Rene Korss <rene.korss@gmail.com>
 */

class Swedbank extends Banklink{

    protected $responseUrl     = 'https://www.swedbank.ee/banklink';
    protected $testResponseUrl = 'http://localhost:8080/banklink/swedbank-common';
    /**
     * Force Swedbank class to use iPizza protocol
     *
     * @param RKD\Banklink\Protocol\iPizza $protocol
     * @param boolean Is in debug mode?
     * @param string Response URL
     *
     * @return void
     */

    public function __construct(iPizza $protocol, $debug = false, $responseUrl = null){
        parent::__construct($protocol, $debug, $responseUrl);
    }

    /**
     * Override encoding field
     */

    protected function getEncodingField(){
        return 'VK_CHARSET';
    }

    /**
     * Swedbank uses UTF-8
     *
     * @return array Array of additiona fields to send to bank
     */
    protected function getAdditionalFields(){
        return array(
            'VK_CHARSET' => $this->requestEncoding
        );
    }
}
