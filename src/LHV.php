<?php
namespace RKD\Banklink;

use RKD\Banklink\Protocol\iPizza;

/**
 * Banklink settings for LHV
 *
 * For more information, please visit: https://www.lhv.ee/en/banking-services/banklink/?l3=en
 *
 * @author  Rene Korss <rene.korss@gmail.com>
 */

class LHV extends Banklink{

    protected $requestUrl     = 'https://www.lhv.ee/banklink';
    protected $testRequestUrl = 'http://localhost:8080/banklink/lhv-common';

    /**
     * Force LHV class to use iPizza protocol
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
     * Override encoding field
     */

    protected function getEncodingField(){
        return 'VK_CHARSET';
    }

    /**
     * LHV uses UTF-8
     *
     * @return array Array of additiona fields to send to bank
     */
    protected function getAdditionalFields(){
        return array(
            'VK_CHARSET' => $this->requestEncoding
        );
    }
}
