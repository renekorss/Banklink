<?php
namespace RKD\Banklink;

use RKD\Banklink\Protocol\iPizza;

/**
 * Banklink settings for Krediidipank
 *
 * For more information, please visit: http://www.krediidipank.ee/business/settlements/bank-link/index.html
 *
 * @author  Rene Korss <rene.korss@gmail.com>
 */

class Krediidipank extends Banklink{

    protected $responseUrl       = 'https://i-pank.krediidipank.ee/teller/maksa';
    protected $testResponseUrl   = 'http://localhost:8080/banklink/krediidipank-common';

    protected $responseEncoding = 'ISO-8859-13';

    /**
     * Force Krediidipank class to use iPizza protocol
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
     * Krediidipank uses UTF-8
     *
     * @return array Array of additiona fields to send to bank
     */
    protected function getAdditionalFields(){
        return array(
            'VK_CHARSET' => $this->requestEncoding
        );
    }
}
