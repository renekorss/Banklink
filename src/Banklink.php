<?php
namespace RKD\Banklink;

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

    protected $responseUrl;
    protected $testResponseUrl;

    protected $requestEncoding  = 'UTF-8';
    protected $responseEncoding = 'ISO-8859-1';

    /**
     * Init banklink
     *
     * @param RKD\Banklink\Protocol Protocol object used
     * @param string Response URL
     */

    public function __construct($protocol, $debug = false, $responseUrl = null){

        $this->protocol = $protocol;

        if($debug){
            $this->responseUrl = $this->testResponseUrl;
        }
        else if($responseUrl){
            $this->responseUrl = $responseUrl;
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

        return $this->data;
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
     * Get request hidden inputs
     */

    public function getRequestInputs(){
        $html = '';

        foreach ($this->data as $key => $value) {
            $html .= vsprintf('<input type="hidden" id="%s" name="%s" value="%s" />', array(strtolower($key), $key, $value))."\n";
        }

        return $html;
    }

    /**
     * Get response URL
     *
     * @return string
     */
    public function getResponseUrl(){
        return $this->responseUrl;
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

