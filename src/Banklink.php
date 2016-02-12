<?php
/**
 * RKD Banklink.
 *
 * @link https://github.com/renekorss/Banklink/
 *
 * @author Rene Korss <rene.korss@gmail.com>
 * @copyright 2015 Rene Korss
 * @license MIT
 */
namespace RKD\Banklink;

use RKD\Banklink\Protocol\Protocol;
use RKD\Banklink\Request\PaymentRequest;
use RKD\Banklink\Request\AuthRequest;

/**
 * Abstract class for every banklink.
 *
 * @author  Rene Korss <rene.korss@gmail.com>
 */
abstract class Banklink
{
    /**
     * Protocol object used for communication.
     *
     * @var RKD\Banklink\Protocol
     */
    protected $protocol;

    /**
     * Request data.
     *
     * @var array
     */
    protected $requestData = null;

    /**
     * Authentication data.
     *
     * @var array
     */
    protected $authData = null;

    /**
     * Request url.
     *
     * @var string
     */
    protected $requestUrl;

    /**
     * Test request url.
     *
     * @var string
     */
    protected $testRequestUrl;

    /**
     * Request encoding.
     *
     * @var string
     */
    protected $requestEncoding = 'UTF-8';

    /**
     * Response encoding.
     *
     * @var string
     */
    protected $responseEncoding = 'ISO-8859-1';

    /**
     * Init banklink.
     *
     * @param RKD\Banklink\Protocol $protocol   Protocol object used
     * @param bool                  $debug      Use banklink in debug mode?
     * @param string                $requestUrl Response URL
     */
    public function __construct(Protocol $protocol, $debug = false, $requestUrl = null)
    {
        $this->protocol = $protocol;

        if ($debug) {
            $this->requestUrl = $this->testRequestUrl;
        } elseif ($requestUrl) {
            $this->requestUrl = $requestUrl;
        }
    }

    /**
     * Get payment object.
     *
     * @param string $orderID  Order ID
     * @param float  $sum      Sum of order
     * @param string $message  Transaction description
     * @param string $language Language
     * @param string $currency Currency. Default: EUR
     * @param string $timezone Timezone. Default: Europe/Tallinn
     *
     * @return RKD\Banklink\Request\PaymentRequest Payment object
     */
    public function getPaymentRequest($orderId, $sum, $message, $language = 'EST', $currency = 'EUR', $timezone = 'Europe/Tallinn')
    {
        if ($this->requestData) {
            return $this->requestData;
        }

        $requestData = $this->protocol->getPaymentRequest($orderId, $sum, $message, $this->requestEncoding, $language, $currency, $timezone);

        // Add additional fields
        $requestData = array_merge($requestData, $this->getAdditionalFields());

        $this->requestData = new PaymentRequest($this->requestUrl, $requestData);

        return $this->requestData;
    }

     /**
      * Get auhtnetication object.
      *
      * @param string $recId Bank identifier
      * @param string $nonce Random nonce
      * @param string $rid Session identifier.
      * @param string $language Language
      * @param string $timezone Timezone. Default: Europe/Tallinn
      *
      * @return RKD\Banklink\Request\AuthRequest Authentication object
      */
    public function getAuthRequest($recId = null, $nonce = null, $rid = null, $language = 'EST', $timezone = 'Europe/Tallinn')
    {
        if ($this->authData) {
            return $this->authData;
        }

        $authData = $this->protocol->getAuthRequest($recId, $nonce, $rid, $this->requestEncoding, $language, $timezone);

        // Add additional fields
        $authData = array_merge($authData, $this->getAdditionalFields());

        $this->authData = new AuthRequest($this->requestUrl, $authData);

        return $this->authData;
    }

    /**
     * Handles response from bank.
     *
     * @param array $responseData Response data from bank
     *
     * @return \Response\PaymentResponse|\Response\AuthResponse Response object, depending on request made
     */
    public function handleResponse(array $responseData)
    {
        return $this->protocol->handleResponse($responseData, $this->getResponseEncoding($responseData));
    }

    /**
     * Get encoding for response, if response data has it.
     *
     * @param array $responseData Response data from bank
     *
     * @return string Encoding
     */
    protected function getResponseEncoding(array $responseData)
    {
        if ($this->getEncodingField() && isset($responseData[$this->getEncodingField()])) {
            return $responseData[$this->getEncodingField()];
        }

        return $this->responseEncoding;
    }

    /**
     * Detect if bank sent us data with encoding field.
     *
     * @return string|null Encoding field name
     */
    protected function getEncodingField()
    {
        return; // @codeCoverageIgnore
    }

    /**
     * Add additional fields.
     *
     * @return array Array of additional fields
     */
    protected function getAdditionalFields()
    {
        return array(); // @codeCoverageIgnore
    }
}
