<?php
/**
 * RKD Banklink.
 *
 * @link https://github.com/renekorss/Banklink/
 *
 * @author Rene Korss <rene.korss@gmail.com>
 * @copyright 2016-2020 Rene Korss
 * @license MIT
 */
namespace RKD\Banklink;

use RKD\Banklink\Protocol\ProtocolInterface;
use RKD\Banklink\Request\AuthRequest;
use RKD\Banklink\Request\PaymentRequest;
use RKD\Banklink\Request\RequestInterface;
use RKD\Banklink\Response\ResponseInterface;
use UnexpectedValueException;

/**
 * Abstract class for every banklink.
 *
 * @author Rene Korss <rene.korss@gmail.com>
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
     * @var mixed
     */
    protected $requestUrl;

    /**
     * Test request url.
     *
     * @var mixed
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
    protected $responseEncoding = 'UTF-8';

    /**
     * Init banklink.
     *
     * @param RKD\Banklink\Protocol $protocol   Protocol object used
     */
    public function __construct(ProtocolInterface $protocol)
    {
        $this->protocol = $protocol;
    }

    /**
     * Activate debug mode. Changes requestUrl to testRequestUrl
     *
     * @return self
     */
    public function debugMode() : self
    {
        return $this->setRequestUrl($this->testRequestUrl);
    }

    /**
     * Set request URL
     *
     * @param string $requestUrl Request URL
     *
     * @return self
     */
    public function setRequestUrl($requestUrl) : self
    {
        $this->requestUrl = $requestUrl;
        return $this;
    }

    /**
     * Get payment object
     *
     * @param int    $orderId           Order ID
     * @param float  $sum               Sum of order
     * @param string $message           Transaction description
     * @param string $language          Language
     * @param string $currency          Currency. Default: EUR
     * @param array  $customRequestData Optional custom request data
     * @param string $timezone          Timezone. Default: Europe/Tallinn
     *
     * @return RKD\Banklink\Request\PaymentRequest Payment object
     */
    public function getPaymentRequest(
        int $orderId,
        float $sum,
        string $message,
        string $language = 'EST',
        string $currency = 'EUR',
        array $customRequestData = [],
        string $timezone = 'Europe/Tallinn'
    ) : RequestInterface {
        if ($this->requestData) {
            return $this->requestData;
        }

        $requestData = $this->protocol->getPaymentRequest(
            $orderId,
            $sum,
            $message,
            $language,
            $currency,
            array_merge($this->getAdditionalRequestFields(), $customRequestData),
            $this->requestEncoding,
            $timezone
        );

        // Add additional fields
        $requestData = array_merge($requestData, $this->getAdditionalFields());

        $this->requestData = new PaymentRequest($this->getRequestUrlFor('payment'), $requestData);

        return $this->requestData;
    }

    /**
     * Get authentication object
     *
     * @param string|null $recId    Bank identifier
     * @param string|null $nonce    Random nonce
     * @param string|null $rid      Session identifier.
     * @param string      $language Language
     * @param string      $timezone Timezone. Default: Europe/Tallinn
     *
     * @return RKD\Banklink\Request\RequestInterface Authentication object
     */
    public function getAuthRequest(
        ?string $recId = null,
        ?string $nonce = null,
        ?string $rid = null,
        string $language = 'EST',
        string $timezone = 'Europe/Tallinn'
    ) : RequestInterface {
        if ($this->authData) {
            return $this->authData;
        }

        $authData = $this->protocol->getAuthRequest($recId, $nonce, $rid, $this->requestEncoding, $language, $timezone);

        // Add additional fields
        $authData = array_merge($authData, $this->getAdditionalFields());

        $this->authData = new AuthRequest($this->getRequestUrlFor('auth'), $authData);

        return $this->authData;
    }

    /**
     * Handles response from bank.
     *
     * @param array $responseData Response data from bank
     *
     * @return RKD\Banklink\Response\ResponseInterface Response object, depending on request made
     */
    public function handleResponse(array $responseData) : ResponseInterface
    {
        return $this->protocol->handleResponse($responseData, $this->getResponseEncoding($responseData));
    }

    /**
     * Get request url based on type
     *
     * @param string $type Request URL type
     *
     * @return string Request URL
     *
     * @throws UnexpectedValueException If requestUrl is in wrong type or not set
     */
    public function getRequestUrlFor(string $type) : string
    {
        if (is_string($this->requestUrl)) {
            return $this->requestUrl;
        } elseif (is_array($this->requestUrl) && array_key_exists($type, $this->requestUrl)) {
            return $this->requestUrl[$type];
        }

        throw new UnexpectedValueException(sprintf('requestUrl is not string or array containing desired type (%s)', $type));
    }

    /**
     * Get encoding for response, if response data has it.
     *
     * @param array $responseData Response data from bank
     *
     * @return string Encoding
     */
    protected function getResponseEncoding(array $responseData) : string
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
    protected function getEncodingField() : string
    {
        return ''; // @codeCoverageIgnore
    }

    /**
     * Add additional fields.
     *
     * @return array Array of additional fields
     */
    protected function getAdditionalFields() : array
    {
        return []; // @codeCoverageIgnore
    }

    /**
     * Additional request fields merged to request data
     *
     * @return array Array of additional request fields to send to bank
     */
    protected function getAdditionalRequestFields() : array
    {
        return []; // @codeCoverageIgnore
    }
}
