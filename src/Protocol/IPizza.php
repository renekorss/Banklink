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
namespace RKD\Banklink\Protocol;

use DateTime;
use DateTimeZone;
use InvalidArgumentException;
use RKD\Banklink\Protocol\Helper\ProtocolHelper;
use RKD\Banklink\Protocol\IPizza\Services;
use RKD\Banklink\Response\AuthResponse;
use RKD\Banklink\Response\PaymentResponse;
use RKD\Banklink\Response\ResponseInterface;
use UnexpectedValueException;

/**
 * Protocol for IPizza based banklinks.
 *
 * @author Rene Korss <rene.korss@gmail.com>
 */
class IPizza implements ProtocolInterface
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
     * Seller account number.
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
     * Is mb_strlen function used to get string length?
     *
     * @var bool
     */
    protected $useMbStrlen = true;

    /**
     * Algorithm used to generate mac
     *
     * @var int|string
     */
    protected $algorithm = OPENSSL_ALGO_SHA1;

    /**
     * Fields
     */
    const FIELD_SERVICE = 'VK_SERVICE';
    const FIELD_VERSION = 'VK_VERSION';
    const FIELD_SND_ID = 'VK_SND_ID';
    const FIELD_STAMP = 'VK_STAMP';
    const FIELD_AMOUNT = 'VK_AMOUNT';
    const FIELD_CURR = 'VK_CURR';
    const FIELD_REF = 'VK_REF';
    const FIELD_MSG = 'VK_MSG';
    const FIELD_RETURN = 'VK_RETURN';
    const FIELD_CANCEL = 'VK_CANCEL';
    const FIELD_DATETIME = 'VK_DATETIME';
    const FIELD_T_DATETIME = 'VK_T_DATETIME';
    const FIELD_LANG = 'VK_LANG';
    const FIELD_NAME = 'VK_NAME';
    const FIELD_ACC = 'VK_ACC';
    const FIELD_MAC = 'VK_MAC';
    const FIELD_RID = 'VK_RID';
    const FIELD_REPLY = 'VK_REPLY';
    const FIELD_NONCE = 'VK_NONCE';
    const FIELD_REC_ID = 'VK_REC_ID';
    const FIELD_AUTO = 'VK_AUTO';
    const FIELD_SND_NAME = 'VK_SND_NAME';
    const FIELD_SND_ACC = 'VK_SND_ACC';
    const FIELD_REC_NAME = 'VK_REC_NAME';
    const FIELD_REC_ACC = 'VK_REC_ACC';
    const FIELD_T_NO = 'VK_T_NO';
    const FIELD_USER_ID = 'VK_USER_ID';
    const FIELD_USER_NAME = 'VK_USER_NAME';
    const FIELD_COUNTRY= 'VK_COUNTRY';
    const FIELD_TOKEN = 'VK_TOKEN';

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

        // Detect which service to use
        if (strlen($sellerName) > 0 && strlen($sellerAccount) > 0) {
            $this->serviceId = Services::PAYMENT_REQUEST_1011;
            return;
        }

        $this->serviceId = Services::PAYMENT_REQUEST_1012;
    }

    /**
     * Set mb_strlen usage
     *
     * @param bool $useMbStrlen Use mb_strlen
     */

    public function useMbStrlen($useMbStrlen)
    {
        $this->useMbStrlen = (boolean)$useMbStrlen;
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
     * @param string $encoding          Encoding
     * @param string $timezone          Timezone. Default: Europe/Tallinn
     *
     * @return array Payment request data
     */
    public function getPaymentRequest(
        int $orderId,
        float $sum,
        string $message,
        string $language = 'EST',
        string $currency = 'EUR',
        array $customRequestData = [],
        string $encoding = 'UTF-8',
        string $timezone = 'Europe/Tallinn'
    ) : array {
        $time = getenv('CI') ? getenv('TEST_DATETIME') : 'now';
        $datetime = new DateTime($time, new DateTimeZone($timezone));

        $data = [
            static::FIELD_SERVICE => $this->serviceId,
            static::FIELD_VERSION => $this->version,
            static::FIELD_SND_ID => $this->sellerId,
            static::FIELD_STAMP => $orderId,
            static::FIELD_AMOUNT => $sum,
            static::FIELD_CURR => $currency,
            static::FIELD_REF => ProtocolHelper::calculateReference($orderId),
            static::FIELD_MSG => $message,
            static::FIELD_RETURN => $this->requestUrl,
            static::FIELD_CANCEL => $this->requestUrl,
            static::FIELD_DATETIME => $datetime->format('Y-m-d\TH:i:sO'),
            static::FIELD_LANG => $language,
        ];

        if (Services::PAYMENT_REQUEST_1011 === $this->serviceId) {
            $data[static::FIELD_NAME] = $this->sellerName;
            $data[static::FIELD_ACC] = $this->sellerAccount;
        }

        // Merge custom data
        if (is_array($customRequestData)) {
            $data = array_merge($data, $customRequestData);
        }

        // Generate signature
        $data[static::FIELD_MAC] = $this->getSignature($data, $encoding);

        return $data;
    }

    /**
     * Get authentication object
     *
     * @param string|null $recId    Bank identifier
     * @param string|null $nonce    Random nonce
     * @param string|null $rid      Session identifier.
     * @param string      $encoding Encoding
     * @param string      $language Language
     * @param string      $timezone Timezone. Default: Europe/Tallinn
     *
     * @return array Authentication request data
     */
    public function getAuthRequest(
        ?string $recId = null,
        ?string $nonce = null,
        ?string $rid = null,
        string $encoding = 'UTF-8',
        string $language = 'EST',
        string $timezone = 'Europe/Tallinn'
    ) : array {
        $time = getenv('CI') ? getenv('TEST_DATETIME') : 'now';
        $datetime = new Datetime($time, new DateTimeZone($timezone));

        $this->serviceId = (is_null($nonce)) ? Services::AUTH_REQUEST_4011 : Services::AUTH_REQUEST_4012;

        $data = [
            static::FIELD_SERVICE => $this->serviceId,
            static::FIELD_VERSION => $this->version,
            static::FIELD_SND_ID => $this->sellerId,
            static::FIELD_RETURN => $this->requestUrl,
            static::FIELD_DATETIME => $datetime->format('Y-m-d\TH:i:sO'),
            static::FIELD_RID => '',
            static::FIELD_LANG => $language,
            static::FIELD_REPLY => Services::AUTH_RESPONSE_3012
        ];

        if (!is_null($nonce)) {
            $data[static::FIELD_SERVICE] = Services::AUTH_REQUEST_4012;
            $data[static::FIELD_NONCE] = $nonce;
            $data[static::FIELD_REC_ID] = $recId;
            unset($data[static::FIELD_REPLY]);
        }

        if (!is_null($rid)) {
            $data[static::FIELD_RID] = $rid;
        }

        // Generate signature
        $data[static::FIELD_MAC] = $this->getSignature($data, $encoding);

        return $data;
    }

    /**
     * Handles response from bank.
     *
     * @param array  $response Response data from bank
     * @param string $encoding     Encoding
     *
     * @return RKD\Banklink\Response\Response Response object, depending on request made
     */
    public function handleResponse(array $response, string $encoding = 'UTF-8') : ResponseInterface
    {
        $success = $this->validateSignature($response, $encoding);

        $service = $response[static::FIELD_SERVICE];
        $servicesClass = static::getServicesClass();

        // Is payment response service?
        if (in_array($service, $servicesClass::getPaymentResponseServices())) {
            return $this->handlePaymentResponse($response, $success);
        }

        // Is authentication response service?
        if (in_array($service, $servicesClass::getAuthenticationResponseServices())) {
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
    protected function handlePaymentResponse(array $responseData, bool $success) : ResponseInterface
    {
        $servicesClass = static::getServicesClass();
        $status = PaymentResponse::STATUS_ERROR;

        if ($success && $responseData[static::FIELD_SERVICE] === $servicesClass::PAYMENT_RESPONSE_SUCCESS) {
            $status = PaymentResponse::STATUS_SUCCESS;
        }

        $response = new PaymentResponse($status, $responseData);
        $response->setOrderId($responseData[static::FIELD_STAMP]);

        if (isset($responseData[static::FIELD_LANG])) {
            $response->setLanguage($responseData[static::FIELD_LANG]);
        }

        if (isset($responseData[static::FIELD_AUTO])) {
            $response->setAutomatic($responseData[static::FIELD_AUTO] === PaymentResponse::RESPONSE_AUTO);
        }

        if (isset($responseData[static::FIELD_MSG])) {
            $response->setMessage($responseData[static::FIELD_MSG]);
        }

        if (PaymentResponse::STATUS_SUCCESS === $status) {
            // IPizza 2015 fallback: SEB has VK_ACC, others VK_REC_ACC
            $receiverAccount = $responseData[static::FIELD_REC_ACC] ?? $responseData[static::FIELD_ACC];

            $response
                ->setSum($responseData[static::FIELD_AMOUNT])
                ->setCurrency($responseData[static::FIELD_CURR])
                ->setSender($responseData[static::FIELD_SND_NAME], $responseData[static::FIELD_SND_ACC])
                ->setReceiver($responseData[static::FIELD_REC_NAME], $receiverAccount)
                ->setTransactionId($responseData[static::FIELD_T_NO])
                ->setTransactionDate($responseData[static::FIELD_T_DATETIME]);
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
    protected function handleAuthResponse(array $responseData, bool $success) : ResponseInterface
    {
        $status = AuthResponse::STATUS_ERROR;
        if ($success) {
            $status = AuthResponse::STATUS_SUCCESS;
        }

        $response = new AuthResponse($status, $responseData);

        if (isset($responseData[static::FIELD_LANG])) {
            $response->setLanguage($responseData[static::FIELD_LANG]);
        }

        if (PaymentResponse::STATUS_SUCCESS === $status) {
            $response
                // Person data
                ->setUserId($responseData[static::FIELD_USER_ID])
                ->setUserName($responseData[static::FIELD_USER_NAME])
                ->setUserCountry($responseData[static::FIELD_COUNTRY])
                ->setToken($responseData[static::FIELD_TOKEN])
                // Request data
                ->setRid($responseData[static::FIELD_RID])
                ->setNonce($responseData[static::FIELD_NONCE])
                ->setAuthDate($responseData[static::FIELD_DATETIME]);
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
    public function getSignature(array $data, string $encoding = 'UTF-8') : string
    {
        $mac = $this->generateSignature($data, $encoding);
        $signature = '';

        if (is_file($this->privateKey)) {
            $privateKey = openssl_pkey_get_private('file://'.$this->privateKey, $this->privateKeyPassword);
        } elseif (is_string($this->privateKey)) {
            $privateKey = openssl_pkey_get_private($this->privateKey, $this->privateKeyPassword);
        }

        if (!$privateKey) {
            throw new UnexpectedValueException('Can not get private key.');
        }

        openssl_sign($mac, $signature, $privateKey, $this->algorithm);
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
    protected function generateSignature(array $data, string $encoding = 'UTF-8') : string
    {
        if (!isset($data[static::FIELD_SERVICE])) {
            throw new InvalidArgumentException(static::FIELD_SERVICE.' key must be in data. Can\'t generate signature.');
        }

        $service = $data[static::FIELD_SERVICE];
        $fields = static::getFields($service);
        $mac = '';

        // VK_REC_ACC fallback to VK_ACC
        if (in_array(static::FIELD_REC_ACC, $fields) && isset($data[static::FIELD_ACC])) {
            $fields[array_search(static::FIELD_REC_ACC, $fields)] = static::FIELD_ACC;
        }

        foreach ($fields as $key) {
            // Check if field exists
            if (!isset($data[$key]) || $data[$key] === false || is_null($data[$key])) {
                throw new UnexpectedValueException(
                    vsprintf('Field %s must be set to use service %s.', [$key, $service])
                );
            }

            $value = $data[$key];
            $length = $this->useMbStrlen ? mb_strlen($value, $encoding) : strlen($value);
            $mac .= str_pad($length, 3, '0', STR_PAD_LEFT).$value;
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
    protected function validateSignature(array $response, string $encoding = 'UTF-8') : bool
    {
        $data = $this->generateSignature($response, $encoding);

        if (is_file($this->publicKey)) {
            $publicKey = openssl_get_publickey('file://'.$this->publicKey);
        } elseif (is_string($this->publicKey)) {
            $publicKey = openssl_get_publickey($this->publicKey);
        }

        if (!$publicKey) {
            throw new UnexpectedValueException('Can not get public key.');
        }

        $this->result = openssl_verify($data, base64_decode($response[static::FIELD_MAC]), $publicKey, $this->algorithm);
        openssl_free_key($publicKey);

        return $this->result === 1;
    }

    /**
     * Set algorithm used to generate mac
     *
     * Should be one of valid values for openssl_sign functions signature_alg parameter
     * @see http://ee1.php.net/manual/en/function.openssl-sign.php
     *
     * @param int|string
     */
    public function setAlgorithm($algorithm) : self
    {
        $this->algorithm = $algorithm;
        return $this;
    }

    /**
     * Get algorithm used to generate mac
     *
     * @return mixed
     */
    public function getAlgorithm()
    {
        return $this->algorithm;
    }

    /**
     * Get fields from Services class
     */
    protected static function getFields($service)
    {
        $servicesClass = static::getServicesClass();
        return $servicesClass::getFields($service);
    }

    /**
     * Get services provider class
     *
     * @return object
     */
    protected static function getServicesClass()
    {
        return Services::class;
    }
}
