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
namespace RKD\Banklink\Protocol;

use RKD\Banklink\Protocol\Helper\ProtocolHelper;
use RKD\Banklink\Protocol\IPizza\Services;
use RKD\Banklink\Response\PaymentResponse;
use RKD\Banklink\Response\AuthResponse;

/**
 * Protocol for IPizza based banklinks.
 *
 * @author Rene Korss <rene.korss@gmail.com>
 */
class IPizza implements Protocol
{
    /**
     * File path or file contents of public key.
     *
     * @var string
     */
    protected $publicKey;

    /**
     * File path or file contents of private key.
     *
     * @var string
     */
    protected $privateKey;

    /**
     * Private key password.
     *
     * @var string
     */
    protected $privateKeyPassword;

    /**
     * Seller id, provided by bank.
     *
     * @var string
     */
    protected $sellerId;

    /**
     * Seller name, mus match with bank account name.
     *
     * @var string
     */
    protected $sellerName;

    /**
     * Selelr account number.
     *
     * @var string
     */
    protected $sellerAccount;

    /**
     * Protocol version used for communication.
     *
     * @var string
     */
    protected $version;

    /**
     * Request url, where data will be sent.
     *
     * @var string
     */
    protected $requestUrl;

    /**
     * Service number used.
     *
     * @var string
     */
    protected $serviceId;

    /**
     * Result of signature validation.
     *
     * @var bool
     */
    protected $result;

    /**
     * Is nb_strlen function used to get string length?
     *
     * @var bool
     */
    protected $useMbStrlen;

    /**
     * Init IPizza protocol.
     *
     * @param string $sellerId           Seller ID (SND ID)
     * @param string $privateKey         Path to private key
     * @param string $privateKeyPassword Private key password, if used
     * @param string $publicKey          Path to public key
     * @param string $requestUrl         Request URL
     * @param string $sellerName         Seller name
     * @param string $sellerAccount      Seller account
     * @param bool   $useMbStrlen        Use mb_strlen
     * @param string $version            Encryption used
     */
    public function __construct(
        $sellerId,
        $privateKey,
        $privateKeyPassword,
        $publicKey,
        $requestUrl,
        $sellerName = null,
        $sellerAccount = null,
        $useMbStrlen = true,
        $version = '008'
    ) {
        $this->privateKey = $privateKey;
        $this->privateKeyPassword = $privateKeyPassword;
        $this->publicKey = $publicKey;

        $this->sellerId = $sellerId;
        $this->sellerName = $sellerName;
        $this->sellerAccount = $sellerAccount;
        $this->version = $version;
        $this->requestUrl = $requestUrl;
        $this->useMbStrlen = $useMbStrlen;

        // Detect which service to use
        $this->serviceId = Services::PAYMENT_REQUEST_1012;

        if (strlen($sellerName) > 0 && strlen($sellerAccount) > 0) {
            $this->serviceId = Services::PAYMENT_REQUEST_1011;
        }
    }

    /**
     * Get payment object.
     *
     * @param string $orderId  Order ID
     * @param float  $sum      Sum of order
     * @param string $message  Transaction description
     * @param string $encoding Encoding
     * @param string $language Language
     * @param string $currency Currency. Default: EUR
     * @param string $timezone Timezone. Default: Europe/Tallinn
     *
     * @return array Payment request data
     */
    public function getPaymentRequest(
        $orderId,
        $sum,
        $message,
        $encoding = 'UTF-8',
        $language = 'EST',
        $currency = 'EUR',
        $timezone = 'Europe/Tallinn'
    ) {
        $time = getenv('CI') ? getenv('TEST_DATETIME') : 'now';
        $datetime = new \Datetime($time, new \DateTimeZone($timezone));

        $data = array(
            'VK_SERVICE' => $this->serviceId,
            'VK_VERSION' => $this->version,
            'VK_SND_ID' => $this->sellerId,
            'VK_STAMP' => $orderId,
            'VK_AMOUNT' => $sum,
            'VK_CURR' => $currency,
            'VK_REF' => ProtocolHelper::calculateReference($orderId),
            'VK_MSG' => $message,
            'VK_RETURN' => $this->requestUrl,
            'VK_CANCEL' => $this->requestUrl,
            'VK_DATETIME' => $datetime->format('Y-m-d\TH:i:sO'),
            'VK_LANG' => $language,
        );

        if (Services::PAYMENT_REQUEST_1011 === $this->serviceId) {
            $data['VK_NAME'] = $this->sellerName;
            $data['VK_ACC'] = $this->sellerAccount;
        }

        // Generate signature
        $data['VK_MAC'] = $this->getSignature($data, $encoding);

        return $data;
    }

    /**
     * Get authnetication object.
     *
     * @param string $recId Bank identifier
     * @param string $nonce Random nonce
     * @param string $rid Session identifier.
     * @param string $encoding Encoding
     * @param string $language Language
     * @param string $timezone Timezone. Default: Europe/Tallinn
     *
     * @return array Authentication request data
     */
    public function getAuthRequest(
        $recId = null,
        $nonce = null,
        $rid = null,
        $encoding = 'UTF-8',
        $language = 'EST',
        $timezone = 'Europe/Tallinn'
    ) {
        $time = getenv('CI') ? getenv('TEST_DATETIME') : 'now';
        $datetime = new \Datetime($time, new \DateTimeZone($timezone));

        $this->serviceId = (is_null($nonce)) ? Services::AUTH_REQUEST_4011 : Services::AUTH_REQUEST_4012;

        $data = array(
            'VK_SERVICE' => $this->serviceId,
            'VK_VERSION' => $this->version,
            'VK_SND_ID' => $this->sellerId,
            'VK_RETURN' => $this->requestUrl,
            'VK_DATETIME' => $datetime->format('Y-m-d\TH:i:sO'),
            'VK_RID' => '',
            'VK_LANG' => $language,
            'VK_REPLY' => Services::AUTH_RESPONSE_3012
        );

        if (!is_null($nonce)) {
            $data['VK_SERVICE'] = Services::AUTH_REQUEST_4012;
            $data['VK_NONCE'] = $nonce;
            $data['VK_REC_ID'] = $recId;
            unset($data['VK_REPLY']);
        }

        if (!is_null($rid)) {
            $data['VK_RID'] = $rid;
        }

        // Generate signature
        $data['VK_MAC'] = $this->getSignature($data, $encoding);

        return $data;
    }

    /**
     * Handles response from bank.
     *
     * @param array  $responseData Response data from bank
     * @param string $encoding     Encoding
     *
     * @return \Response\PaymentResponse|\Response\AuthResponse Response object, depending on request made
     */
    public function handleResponse(array $response, $encoding = 'UTF-8')
    {
        $success = $this->validateSignature($response, $encoding);

        $service = $response['VK_SERVICE'];

        // Is payment response service?
        if (in_array($service, Services::getPaymentResponseServices())) {
            return $this->handlePaymentResponse($response, $success);
        }

        // Is authentication response service?
        if (in_array($service, Services::getAuthenticationResponseServices())) {
            return $this->handleAuthResponse($response, $success);
        }
    } // @codeCoverageIgnore

    /**
     * Get payment response.
     *
     * @param array $responseData Response data from bank
     * @param bool  $success      Signature validated?
     *
     * @return \RKD\Banklink\Response\PaymentResponse
     */
    protected function handlePaymentResponse(array $responseData, $success)
    {
        $status = PaymentResponse::STATUS_ERROR;

        if ($success && $responseData['VK_SERVICE'] === Services::PAYMENT_RESPONSE_SUCCESS) {
            $status = PaymentResponse::STATUS_SUCCESS;
        }

        $response = new PaymentResponse($status, $responseData);
        $response->setOrderId($responseData['VK_STAMP']);

        if (isset($responseData['VK_LANG'])) {
            $response->setLanguage($responseData['VK_LANG']);
        }

        if (PaymentResponse::STATUS_SUCCESS === $status) {
            $response->setSum($responseData['VK_AMOUNT']);
            $response->setCurrency($responseData['VK_CURR']);
            $response->setSender($responseData['VK_SND_NAME'], $responseData['VK_SND_ACC']);
            $response->setTransactionId($responseData['VK_T_NO']);
            $response->setTransactionDate($responseData['VK_T_DATETIME']);
        }

        return $response;
    }

    /**
     * Get authentication response.
     *
     * @param array $responseData Response data from bank
     * @param bool  $success      Signature validated?
     *
     * @return \RKD\Banklink\Response\AuthResponse
     */
    protected function handleAuthResponse(array $responseData, $success)
    {
        $status = AuthResponse::STATUS_ERROR;
        if ($success) {
            $status = AuthResponse::STATUS_SUCCESS;
        }

        $response = new AuthResponse($status, $responseData);

        if (isset($responseData['VK_LANG'])) {
            $response->setLanguage($responseData['VK_LANG']);
        }

        if (PaymentResponse::STATUS_SUCCESS === $status) {
            // Person data
            $response->setUserId($responseData['VK_USER_ID']);
            $response->setUserName($responseData['VK_USER_NAME']);
            $response->setUserCountry($responseData['VK_COUNTRY']);
            $response->setToken($responseData['VK_TOKEN']);

            // Request data
            $response->setRid($responseData['VK_RID']);
            $response->setNonce($responseData['VK_NONCE']);
            $response->setAuthDate($responseData['VK_DATETIME']);
        }

        return $response;
    }

    /**
     * Generates signature for request.
     *
     * @param array  $data     Request data
     * @param string $encoding Encoding
     *
     * @return string Signature
     */
    protected function getSignature(array $data, $encoding = 'UTF-8')
    {
        $mac = $this->generateSignature($data, $encoding);

        if (is_file($this->privateKey)) {
            $privateKey = openssl_pkey_get_private('file://'.$this->privateKey, $this->privateKeyPassword);
        } elseif(is_string($this->privateKey)) {
            $privateKey = openssl_pkey_get_private($this->privateKey, $this->privateKeyPassword);
        }

        if (!$privateKey) {
            throw new \UnexpectedValueException('Can not get private key.');
        }

        openssl_sign($mac, $signature, $privateKey);
        openssl_free_key($privateKey);

        $result = base64_encode($signature);

        return $result;
    }

    /**
     * Generate MAC string from array of fields.
     *
     * @param array  $data     Array of VK_* fields
     * @param string $encoding Encoding
     *
     * @return string MAC key
     */
    protected function generateSignature(array $data, $encoding = 'UTF-8')
    {
        $service = $data['VK_SERVICE'];
        $fields = Services::getFields($service);
        $mac = '';

        foreach ($fields as $key) {
            // Check if field exists
            if (!isset($data[$key])) {
                throw new \UnexpectedValueException(
                    vsprintf('Field %s must be set to use service %s.', array($key, $service))
                );
            }

            $value = $data[$key];
            $length = $this->useMbStrlen ? mb_strlen($value, $encoding) : strlen($value);
            $mac    .= str_pad($length, 3, '0', STR_PAD_LEFT).$value;
        }

        return $mac;
    }

    /**
     * Validate bank signature.
     *
     * @param array  $response Array of VK_* fields
     * @param string $encoding Encoding
     *
     * @return bool True on success, false otherwise
     */
    protected function validateSignature(array $response, $encoding = 'UTF-8')
    {
        $data = $this->generateSignature($response, $encoding);

        if (is_file($this->publicKey)) {
            $publicKey = openssl_get_publickey('file://'.$this->publicKey);
        } elseif(is_string($this->publicKey)) {
            $publicKey = openssl_get_publickey($this->publicKey);
        }

        if (!$publicKey) {
            throw new \UnexpectedValueException('Can not get public key.');
        }

        $this->result = openssl_verify($data, base64_decode($response['VK_MAC']), $publicKey);
        openssl_free_key($publicKey);

        return $this->result === 1;
    }
}
