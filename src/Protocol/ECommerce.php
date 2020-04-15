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
use RKD\Banklink\Protocol\Helper\ProtocolHelper;
use RKD\Banklink\Protocol\ProtocolTrait\NoAuthTrait;
use RKD\Banklink\Response\PaymentResponse;
use RKD\Banklink\Response\ResponseInterface;
use UnexpectedValueException;

/**
 * Protocol for ECommerce payment.
 *
 * @author Rene Korss <rene.korss@gmail.com>
 */
class ECommerce implements ProtocolInterface
{
    // No authentication for this protocol
    use NoAuthTrait;

    /**
     * Successful response code.
     *
     * @var string
     */
    const PAYMENT_RESPONSE_SUCCESS = '000';

    /**
     * Abort (user aborted payment) response code.
     *
     * @var string
     */
    const PAYMENT_RESPONSE_ABORT = '017';

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
     * Algorithm used to generate mac
     *
     * @var int|string
     */
    protected $algorithm = OPENSSL_ALGO_SHA1;

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
        $datetime = new Datetime($time, new DateTimeZone($timezone));

        $data = [
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
        ];

        // Merge custom data
        if (is_array($customRequestData)) {
            $data = array_merge($data, $customRequestData);
        }

        // If additionalinfo is sent it needs to be included in MAC calculation
        // So remove it if empty
        // Data format (max length 128): key:value;[key:value;]*
        // Key and value must not contain : and ; but no checking done here
        // Example (reference number): refnr:12345678907;
        // Information here: https://www.estcard.ee/doc/ecom.html (doc version 1.1.3, date 2019-03-13)
        if (strlen($data['additionalinfo']) < 1) {
            unset($data['additionalinfo']);
        }

        // Generate signature
        $data['mac'] = $this->getSignature($data, $encoding);

        return $data;
    }

    /**
     * Get payment response.
     *
     * @param array $responseData Response data from bank
     * @param bool  $success      Signature validated?
     *
     * @return \RKD\Banklink\Response\Response
     */
    protected function handlePaymentResponse(array $responseData, bool $success) : ResponseInterface
    {
        $status = PaymentResponse::STATUS_ERROR;

        if ($success && $responseData['respcode'] === self::PAYMENT_RESPONSE_SUCCESS) {
            $status = PaymentResponse::STATUS_SUCCESS;
        }

        $response = new PaymentResponse($status, $responseData);
        $response->setOrderId($responseData['ecuno']);

        if (isset($responseData['auto'])) {
            $response->setAutomatic(strtoupper($responseData['auto']) === PaymentResponse::RESPONSE_AUTO);
        }

        if (isset($responseData['msgdata'])) {
            $response->setMessage($responseData['msgdata']);
        }

        if (PaymentResponse::STATUS_SUCCESS === $status) {
            $response
                ->setSum(round($responseData['eamount'] / 100, 2))
                ->setCurrency($responseData['cur'])
                ->setTransactionId($responseData['receipt_no'])
                ->setTransactionDate((new Datetime($responseData['datetime']))->format('Y-m-d\TH:i:s'));
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
    protected function getSignature(array $data, string $encoding = 'UTF-8') : string
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
     *
     * Supress CyclomaticComplexity because we can't really do anything without
     * modifing our logic
     *
     * @SuppressWarnings(PHPMD.CyclomaticComplexity)
     */
    protected function generateSignature(array $data, string $encoding = 'UTF-8') : string
    {
        // Request mac
        $fields = [
          'ver',
          'id',
          'ecuno',
          'eamount',
          'cur',
          'datetime',
          'feedBackUrl',
          'delivery'
        ];

        if (isset($data['respcode'])) {
            // Response mac
            $fields = [
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
            ];

            $data['receipt_no'] = ProtocolHelper::mbStrPad($data['receipt_no'], 6, "0", STR_PAD_LEFT, $encoding);
            $data['msgdata'] = ProtocolHelper::mbStrPad(
                $data['respcode'] === self::PAYMENT_RESPONSE_ABORT && strlen($data['msgdata']) == 0 ? ' ' : $data['msgdata'],
                40,
                " ",
                STR_PAD_RIGHT,
                $encoding
            );
            $data['respcode'] = ProtocolHelper::mbStrPad($data['respcode'], 3, "0", STR_PAD_LEFT, $encoding);
            $data['actiontext'] = ProtocolHelper::mbStrPad($data['actiontext'], 40, " ", STR_PAD_RIGHT, $encoding);
        }

        if (isset($data['feedBackUrl'])) {
            $data['feedBackUrl']   = ProtocolHelper::mbStrPad($data['feedBackUrl'], 128);
        }

        if (isset($data['additionalinfo'])) {
            $fields[] = 'additionalinfo';
            $data['additionalinfo']   = ProtocolHelper::mbStrPad($data['additionalinfo'], 128);
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
            if (!isset($data[$key]) || $data[$key] === false || is_null($data[$key])) {
                throw new UnexpectedValueException(
                    vsprintf('Field %s must be set to use ECommerce protocol.', [$key])
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

        $this->result = openssl_verify($data, pack('H*', $response['mac']), $publicKey, $this->algorithm);
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
}
