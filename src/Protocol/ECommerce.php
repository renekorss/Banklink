<?php
/**
 * RKD Banklink.
 *
 * @link https://github.com/renekorss/Banklink/
 *
 * @author Rene Korss <rene.korss@gmail.com>
 * @copyright 2016 Rene Korss
 * @license MIT
 */
namespace RKD\Banklink\Protocol;

use RKD\Banklink\Protocol\Helper\ProtocolHelper;
use RKD\Banklink\Response\PaymentResponse;

/**
 * Protocol for ECommerce payment.
 *
 * @author Rene Korss <rene.korss@gmail.com>
 */
class ECommerce implements Protocol
{
    /**
     * Successful response code.
     *
     * @var string
     */
    const PAYMENT_RESPONSE_SUCCESS = '000';

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
     * Result of signature validation.
     *
     * @var bool
     */
    protected $result;

    /**
     * Init IPizza protocol.
     *
     * @param string $sellerId           Seller ID (ecom service-id)
     * @param string $privateKey         Path to private key
     * @param string $privateKeyPassword Private key password, if used
     * @param string $publicKey          Path to public key
     * @param string $requestUrl         Request URL
     * @param string $version            Encryption used
     */
    public function __construct(
        $sellerId,
        $privateKey,
        $privateKeyPassword,
        $publicKey,
        $requestUrl,
        $version = '004'
    ) {
        $this->privateKey = $privateKey;
        $this->privateKeyPassword = $privateKeyPassword;
        $this->publicKey = $publicKey;

        $this->sellerId = $sellerId;
        $this->version = $version;
        $this->requestUrl = $requestUrl;
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
            'lang' => ProtocolHelper::langToISO6391($language),
            'action' => 'gaf',
            'ver' => $this->version,
            'id' => $this->sellerId,
            'ecuno' => $orderId,
            'eamount' => $sum * 100, // Must be in cents
            'cur' => $currency,
            'datetime' => $datetime->format('YmdHis'),
            'charEncoding' => $encoding,
            'feedBackUrl' => $this->requestUrl,
            'delivery' => 'S',
            'additionalinfo' => $message
        );

        // Generate signature
        $data['mac'] = $this->getSignature($data, $encoding);

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
        throw new \LogicException('ECommerce protocol dosen\'t support authentication.');
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
        return $this->handlePaymentResponse($response, $success);
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

        if ($success && $responseData['respcode'] === self::PAYMENT_RESPONSE_SUCCESS) {
            $status = PaymentResponse::STATUS_SUCCESS;
        }

        $response = new PaymentResponse($status, $responseData);
        $response->setOrderId($responseData['ecuno']);

        if (PaymentResponse::STATUS_SUCCESS === $status) {
            $response->setSum(round($responseData['eamount'] / 100, 2));
            $response->setCurrency($responseData['cur']);
            $response->setTransactionId($responseData['receipt_no']);

            $datetime = new \Datetime($responseData['datetime']);
            $response->setTransactionDate($datetime->format('Y-m-d\TH:i:s'));
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
        } elseif (is_string($this->privateKey)) {
            $privateKey = openssl_pkey_get_private($this->privateKey, $this->privateKeyPassword);
        }

        if (!$privateKey) {
            throw new \UnexpectedValueException('Can not get private key.');
        }

        openssl_sign($mac, $signature, $privateKey);
        openssl_free_key($privateKey);

        $result = bin2hex($signature);

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
        // Request mac
        $fields = array(
          'ver',
          'id',
          'ecuno',
          'eamount',
          'cur',
          'datetime',
          'feedBackUrl',
          'delivery'
        );

        if (isset($data['respcode'])) {
            // Response mac
            $fields = array(
              'ver',
              'id',
              'ecuno',
              'receipt_no',
              'eamount',
              'cur',
              'respcode',
              'datetime',
              'msgdata',
              'actiontext'
            );

            $data['receipt_no'] = ProtocolHelper::mbStrPad($data['receipt_no'], 6, "0", STR_PAD_LEFT, $encoding);
            $data['respcode'] = ProtocolHelper::mbStrPad($data['respcode'], 3, "0", STR_PAD_LEFT, $encoding);
            $data['msgdata'] = ProtocolHelper::mbStrPad($data['msgdata'], 40, " ", STR_PAD_RIGHT, $encoding);
            $data['actiontext'] = ProtocolHelper::mbStrPad($data['actiontext'], 40, " ", STR_PAD_RIGHT, $encoding);
        }

        if (isset($data['feedBackUrl'])) {
            $data['feedBackUrl']   = ProtocolHelper::mbStrPad($data['feedBackUrl'], 128);
        }

        // Pad to correct length
        $data['ver']      = ProtocolHelper::mbStrPad($data['ver'], 3, "0", STR_PAD_LEFT, $encoding);
        $data['id']       = ProtocolHelper::mbStrPad($data['id'], 10, " ", STR_PAD_RIGHT, $encoding);
        $data['ecuno']    = ProtocolHelper::mbStrPad($data['ecuno'], 12, "0", STR_PAD_LEFT, $encoding);
        $data['eamount']  = ProtocolHelper::mbStrPad($data['eamount'], 12, "0", STR_PAD_LEFT, $encoding);
        $data['cur']      = ProtocolHelper::mbStrPad($data['cur'], 3, " ", STR_PAD_RIGHT, $encoding);
        $data['datetime'] = ProtocolHelper::mbStrPad($data['datetime'], 14, " ", STR_PAD_RIGHT, $encoding);

        $mac = '';

        foreach ($fields as $key) {
            // Check if field exists
            if (!isset($data[$key])) {
                throw new \UnexpectedValueException(
                    vsprintf('Field %s must be set to use ECommerce protocol.', array($key))
                );
            }

            $mac .= $data[$key];
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
        } elseif (is_string($this->publicKey)) {
            $publicKey = openssl_get_publickey($this->publicKey);
        }

        if (!$publicKey) {
            throw new \UnexpectedValueException('Can not get public key.');
        }

        $this->result = openssl_verify($data, pack('H*', $response['mac']), $publicKey);
        openssl_free_key($publicKey);

        return $this->result === 1;
    }
}
