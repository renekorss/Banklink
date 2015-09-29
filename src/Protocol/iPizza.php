<?php
namespace RKD\Banklink\Protocol;

use RKD\Banklink\Protocol\iPizza\Services;
use RKD\Banklink\Response\PaymentResponse;

/**
 * Protocol for iPizza based banklinks
 *
 * @author Rene Korss <rene.korss@gmail.com>
 */

class iPizza implements Protocol{

    protected $publicKey;
    protected $privateKey;
    protected $privateKeyPassword;

    protected $sellerId;
    protected $sellerName;
    protected $sellerAccount;
    protected $version;
    protected $responseUrl;
    protected $serviceId;

    protected $result;

    protected $useMbStrlen;

    /**
     * Init iPizza protocol
     *
     * @param string Seller ID (SND ID)
     * @param string Path to private key
     * @param string Path to public key
     * @param string Resonse URL
     * @param boolean Use mb_strlen
     * @param string Encryption used
     * @param string Seller name
     * @param string Seller account number
     *
     * @return void
     */

    public function __construct($sellerId, $privateKey, $privateKeyPassword, $publicKey, $responseUrl, $sellerName = null, $sellerAccount = null, $useMbStrlen = false, $version = '008'){
        $this->privateKey         = $privateKey;
        $this->privateKeyPassword = $privateKeyPassword;
        $this->publicKey          = $publicKey;

        $this->sellerId           = $sellerId;
        $this->sellerName         = $sellerName;
        $this->sellerAccount      = $sellerAccount;
        $this->version            = $version;
        $this->responseUrl        = $responseUrl;

        // Detect which service to use
        $this->serviceId          = (strlen($sellerName) > 0 && strlen($sellerAccount) > 0) ? Services::PAYMENT_REQUEST_1011 : Services::PAYMENT_REQUEST_1012;
    }

    /**
     * Get data for payment
     *
     * @param string Order ID
     * @param float Sum of order
     * @param string Message
     * @param string Encoding
     * @param string Language
     * @param string Currency
     * @param string Timezone
     */

    public function getPaymentRequestData($orderId, $sum, $message, $encoding = 'UTF-8', $language = 'EST', $currency = 'EUR', $timezone = 'Europe/Tallinn'){

        $datetime = new \Datetime('now', new \DateTimeZone($timezone));

        $data = array(
            'VK_SERVICE'  => $this->serviceId,
            'VK_VERSION'  => $this->version,
            'VK_SND_ID'   => $this->sellerId,
            'VK_STAMP'    => $orderId,
            'VK_AMOUNT'   => $sum,
            'VK_CURR'     => $currency,
            'VK_REF'      => $orderId,
            'VK_MSG'      => $message,
            'VK_RETURN'   => $this->responseUrl,
            'VK_CANCEL'   => $this->responseUrl,
            'VK_DATETIME' => $datetime->format('Y-m-d\TH:i:sO'),
            'VK_LANG'     => $language
        );

        if(Services::PAYMENT_REQUEST_1011 === $this->serviceId){
            $data['VK_NAME'] = $this->sellerName;
            $data['VK_ACC']  = $this->sellerAccount;
        }

        // Generate signature
        $data['VK_MAC'] = $this->getSignature($data);

        return $data;
    }

    /**
     * Handle response from bank
     */

    public function handleResponse(array $response, $encoding = 'UTF-8'){
        $success = $this->validateSignature($response, $encoding);

        $service = $response['VK_SERVICE'];

        if(in_array($service, Services::getPaymentResponseServices())){
            return $this->handlePaymentResponse($response, $success);
        }

        throw new \UnexpectedValueException(sprintf('Service %s is not supported.', $service));
    }


    /**
     * Get payment response
     *
     * @param array Response data from bank
     * @param boolean Signature validated?
     *
     * @return \RKD\Banklink\Response\PaymentResponse
     */

    public function handlePaymentResponse(array $responseData, $success){
        if($success && $responseData['VK_SERVICE'] === Services::PAYMENT_RESPONSE_SUCCESS){
            $status = PaymentResponse::STATUS_SUCCESS;
        }
        else{
            $status = PaymentResponse::STATUS_ERROR;
        }

        $response          = new PaymentResponse($status, $responseData);
        $response->setOrderId($responseData['VK_STAMP']);

        if(PaymentResponse::STATUS_SUCCESS === $status){
            $response->setSum($responseData['VK_AMOUNT']);
            $response->setCurrency($responseData['VK_CURR']);
            $response->setSender($responseData['VK_SND_NAME'], $responseData['VK_SND_ACC']);
            $response->setTransactionId($responseData['VK_T_NO']);
            $response->setTransactionDate($responseData['VK_T_DATETIME']);
        }

        return $response;
    }

    /**
     * Generates signature for request
     *
     * @param
     */

    protected function getSignature(array $data, $encoding = 'UTF-8'){
        $mac        = $this->generateSignature($data, $encoding);

        if(is_file($this->privateKey)){
            $privateKey = openssl_pkey_get_private('file://'.$this->privateKey, $this->privateKeyPassword);
        }
        else{
            $privateKey = openssl_pkey_get_private($this->privateKey, $this->privateKeyPassword);
        }

        openssl_sign($mac, $signature, $privateKey);
        openssl_free_key($privateKey);

        $result  = base64_encode($signature);

        return $result;
    }

    /**
     * Generate MAC string from array of fields
     *
     * @param array Array of VK_* fields
     * @return string MAC key
     */

    protected function generateSignature(array $data, $encoding = 'UTF-8'){

        $service = $data['VK_SERVICE'];
        $fields  = Services::getFields($service);
        $mac     = '';

        foreach($fields as $key){

            // Check if field exists
            if(!isset($data[$key])){
                throw new \UnexpectedValueException(vsprintf('Field %s must have value to use service %s.', array($key, $service)));
            }

            $value  = $data[$key];
            $length = $this->useMbStrlen ? mb_strlen($value, $encoding) : strlen($value);
            $mac    .= str_pad($length, 3, '0', STR_PAD_LEFT) . $value;
        }

        return $mac;
    }

    /**
     * Validate bank signature
     *
     * @param array Array of VK_* fields
     *
     * @return boolean True on success, false otherwise
     */

    protected function validateSignature(array $response, $encoding = 'UTF-8'){
        $data         = $this->generateSignature($response);

        if(is_file($this->publicKey)){
            $privateKey = openssl_get_publickey('file://'.$this->publicKey);
        }
        else{
            $privateKey = openssl_get_publickey($this->publicKey);
        }

        $this->result = openssl_verify($data, base64_decode($response['VK_MAC']), $publicKey);
        openssl_free_key($publicKey);

        return $this->result === 1;
    }
}
