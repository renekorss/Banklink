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
 * Banklink settings for SEB
 *
 * For more information, please visit: http://www.seb.ee/eng/business/everyday-banking/collection-payments/collection-payments-internet
 *
 * @author  Rene Korss <rene.korss@gmail.com>
 */

class SEB extends Banklink{

    /**
     * Request url
     * @var string
     */
    protected $requestUrl     = 'https://www.seb.ee/cgi-bin/unet3.sh/un3min.r';

    /**
     * Test request url
     * @var string
     */
    protected $testRequestUrl = 'http://localhost:8080/banklink/seb-common';

    /**
     * Response encoding
     * @var string
     */
    protected $requestEncoding = 'ISO-8859-1';

    /**
     * Force SEB class to use iPizza protocol
     *
     * @param RKD\Banklink\Protocol\iPizza $protocol Protocol used
     * @param boolean $debug Is in debug mode?
     * @param string $requestUrl Request URL
     */

    public function __construct(iPizza $protocol, $debug = false, $requestUrl = null){
        parent::__construct($protocol, $debug, $requestUrl);
    }

    /**
     * Override encoding field
     */

    protected function getEncodingField(){
        return 'VK_ENCODING';
    }

    /**
     * SEB uses UTF-8
     *
     * @return array Array of additional fields to send to bank
     */
    protected function getAdditionalFields(){
        return array(
            'VK_ENCODING' => $this->requestEncoding
        );
    }
}
