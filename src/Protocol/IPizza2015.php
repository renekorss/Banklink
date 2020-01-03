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

use RKD\Banklink\Protocol\Helper\ProtocolHelper;
use RKD\Banklink\Protocol\IPizza\Services2015;
use RKD\Banklink\Protocol\ProtocolTrait\NoAuthTrait;

/**
 * Protocol for IPizza based banklinks.
 *
 * @author Rene Korss <rene.korss@gmail.com>
 */
class IPizza2015 extends IPizza
{
    // No authentication for this protocol
    use NoAuthTrait;

    /**
     * Fields
     */
    const FIELD_T_DATETIME = 'VK_T_DATE';

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
        parent::__construct(
            $sellerId,
            $privateKey,
            $privateKeyPassword,
            $publicKey,
            $requestUrl,
            $sellerName,
            $sellerAccount,
            $version
        );

        // Detect which service to use
        if (strlen($sellerName) > 0 && strlen($sellerAccount) > 0) {
            $this->setServiceId(Services2015::PAYMENT_REQUEST_1001);
            return;
        }

        $this->setServiceId(Services2015::PAYMENT_REQUEST_1002);
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
        string $language = 'ENG',
        string $currency = 'EUR',
        array $customRequestData = [],
        string $encoding = 'UTF-8',
        string $timezone = 'Europe/Tallinn'
    ) : array {
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
            static::FIELD_LANG => $language,
        ];

        if (in_array($this->serviceId, [
            Services2015::PAYMENT_REQUEST_1001,
            Services2015::PAYMENT_REQUEST_2001
        ])) {
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
     * Get services provider class
     *
     * @return object
     */
    protected static function getServicesClass()
    {
        return Services2015::class;
    }

    /**
     * Set service id
     *
     * @param string $serviceId Service ID
     *
     * @return self
     */
    public function setServiceId($serviceId) : self
    {
        $this->serviceId = $serviceId;
        return $this;
    }
}
