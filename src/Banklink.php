<?php
namespace RKD\Banklink;

use RKD\Banklink\Protocol;
use RKD\Banklink\Request;

/**
 * RKD Banklink
 *
 * @package RKDBanklink
 * @link https://github.com/renekorss/Banklink/
 * @author Rene Korss <rene.korss@gmail.com>
 * @copyright 2015 Rene Korss
 * @license MIT
 */

abstract class Banklink{

    protected $protocol;

    protected $data = null;

    protected $requestUrl;
    protected $testRequestUrl;

    protected $requestEncoding  = 'UTF-8';
    protected $responseEncoding = 'ISO-8859-1';

    /**
     * Init banklink
     *
     * @param RKD\Banklink\Protocol Protocol object used
     * @param string Response URL
     */

    public function __construct(Protocol $protocol, $debug = false, $requestUrl = null){

        $this->protocol = $protocol;

        if($debug){
            $this->requestUrl = $this->testRequestUrl;
        }
        else if($requestUrl){
            $this->requestUrl = $requestUrl;
        }
    }

     /**
     * Get data for payment
     *
     * @param string Order ID
     * @param float Sum of order
     * @param string Message
     * @param string Language
     * @param string Currency
     * @param string Timezone
     */

    public function getPaymentRequestData($orderId, $sum, $message, $language = 'EST', $currency = 'EUR', $timezone = 'Europe/Tallinn'){

        if($this->data){
            return $this->data;
        }

        $this->data = $this->protocol->getPaymentRequestData($orderId, $sum, $message, $this->requestEncoding, $language, $currency, $timezone);

        // Add additional fields
        $this->data = array_merge($this->data, $this->getAdditionalFields());

        return new PaymentRequest($this->requestUrl, $this->data);
    }

    /**
     * @param array $responseData
     *
     * @return \Banklink\Response\Response
     */

    public function handleResponse(array $responseData){
        return $this->protocol->handleResponse($responseData, $this->getResponseEncoding($responseData));
    }

    /**
     * Assuming response data may have some additional field to specify encoding, this method can be overriden
     *
     * @param array $responseData
     *
     * @return string
     */

    protected function getResponseEncoding(array $responseData){
        if ($this->getEncodingField() && isset($responseData[$this->getEncodingField()])) {
            return $responseData[$this->getEncodingField()];
        }

        return $this->responseEncoding;
    }

    /**
     * Get response URL
     *
     * @return string
     */
    public function getrequestUrl(){
        return $this->requestUrl;
    }

    /**
     * Detect if bank sent us field with encoding
     *
     * @return string | null
     */

    protected function getEncodingField(){
        return null;
    }

    /**
     * Add additional fields
     *
     * @return array Array of additional fields
     */

    abstract protected function getAdditionalFields();
}

