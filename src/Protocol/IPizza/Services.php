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
namespace RKD\Banklink\Protocol\IPizza;

use UnexpectedValueException;

/**
 * IPizza protocol services.
 *
 * @author Rene Korss <rene.korss@gmail.com>
 */
final class Services
{
    // Payment
    /**
     * Request 1011.
     *
     * @var string
     */
    const PAYMENT_REQUEST_1011 = '1011';

    /**
     * Request 1012
     * Dosen't require name and account number.
     *
     * @var string
     */
    const PAYMENT_REQUEST_1012 = '1012';

    /**
     * Successful response service.
     *
     * @var string
     */
    const PAYMENT_RESPONSE_SUCCESS = '1111';

    /**
     * Failed response service
     * Dosen't require name and account number.
     *
     * @var string
     */
    const PAYMENT_RESPONSE_FAILED = '1911';

    // Authentication
    /**
     * Request 4012.
     *
     * @var string
     */
    const AUTH_REQUEST_4012 = '4012';

    /**
     * Response 3013.
     *
     * @var string
     */
    const AUTH_RESPONSE_3013 = '3013';

    /**
     * Request 4011.
     *
     * @var string
     */
    const AUTH_REQUEST_4011 = '4011';

    /**
     * Response 3012.
     *
     * @var string
     */
    const AUTH_RESPONSE_3012 = '3012';

    /**
     * Get fields required for service.
     *
     * @param string $serviceId Service number
     *
     * @return array Array of fields for service
     *
     * @throws UnexpectedValueException If service is not supported
     *
     * Keep it readable
     * @SuppressWarnings(PHPMD.ExcessiveMethodLength)
     */
    public static function getFields(string $serviceId) : array
    {
        switch ($serviceId) {
            case self::PAYMENT_REQUEST_1011:
                return [
                    'VK_SERVICE',
                    'VK_VERSION',
                    'VK_SND_ID',
                    'VK_STAMP',
                    'VK_AMOUNT',
                    'VK_CURR',
                    'VK_ACC',
                    'VK_NAME',
                    'VK_REF',
                    'VK_MSG',
                    'VK_RETURN',
                    'VK_CANCEL',
                    'VK_DATETIME',
                ];
                break;
            case self::PAYMENT_REQUEST_1012:
                return [
                    'VK_SERVICE',
                    'VK_VERSION',
                    'VK_SND_ID',
                    'VK_STAMP',
                    'VK_AMOUNT',
                    'VK_CURR',
                    'VK_REF',
                    'VK_MSG',
                    'VK_RETURN',
                    'VK_CANCEL',
                    'VK_DATETIME',
                ];
                break;
            case self::PAYMENT_RESPONSE_SUCCESS:
                return [
                    'VK_SERVICE',
                    'VK_VERSION',
                    'VK_SND_ID',
                    'VK_REC_ID',
                    'VK_STAMP',
                    'VK_T_NO',
                    'VK_AMOUNT',
                    'VK_CURR',
                    'VK_REC_ACC',
                    'VK_REC_NAME',
                    'VK_SND_ACC',
                    'VK_SND_NAME',
                    'VK_REF',
                    'VK_MSG',
                    'VK_T_DATETIME',
                ];
                break;
            case self::PAYMENT_RESPONSE_FAILED:
                return [
                    'VK_SERVICE',
                    'VK_VERSION',
                    'VK_SND_ID',
                    'VK_REC_ID',
                    'VK_STAMP',
                    'VK_REF',
                    'VK_MSG',
                ];
                break;
            case self::AUTH_REQUEST_4011:
                return [
                    'VK_SERVICE',
                    'VK_VERSION',
                    'VK_SND_ID',
                    'VK_REPLY',
                    'VK_RETURN',
                    'VK_DATETIME',
                    'VK_RID',
                ];
                break;
            case self::AUTH_REQUEST_4012:
                return [
                    'VK_SERVICE',
                    'VK_VERSION',
                    'VK_SND_ID',
                    'VK_REC_ID',
                    'VK_NONCE',
                    'VK_RETURN',
                    'VK_DATETIME',
                    'VK_RID',
                ];
                break;
            case self::AUTH_RESPONSE_3012:
                return [
                    'VK_SERVICE',
                    'VK_VERSION',
                    'VK_USER',
                    'VK_DATETIME',
                    'VK_SND_ID',
                    'VK_REC_ID',
                    'VK_USER_NAME',
                    'VK_USER_ID',
                    'VK_COUNTRY',
                    'VK_OTHER',
                    'VK_TOKEN',
                    'VK_RID',
                ];
                break;
            case self::AUTH_RESPONSE_3013:
                return [
                    'VK_SERVICE',
                    'VK_VERSION',
                    'VK_DATETIME',
                    'VK_SND_ID',
                    'VK_REC_ID',
                    'VK_NONCE',
                    'VK_USER_NAME',
                    'VK_USER_ID',
                    'VK_COUNTRY',
                    'VK_OTHER',
                    'VK_TOKEN',
                    'VK_RID',
                ];
                break;
            default:
                throw new UnexpectedValueException(sprintf('Service %s is not supported.', $serviceId));
                break;
        }
    }

    /**
     * Get supported payment response services.
     *
     * @return array Array of payments services ID-s
     */
    public static function getPaymentResponseServices() : array
    {
        return [
            self::PAYMENT_RESPONSE_SUCCESS,
            self::PAYMENT_RESPONSE_FAILED,
        ];
    }

    /**
     * Get supported authentication response services.
     *
     * @return array Array of payments services ID-s
     */
    public static function getAuthenticationResponseServices() : array
    {
        return [
            self::AUTH_RESPONSE_3012,
            self::AUTH_RESPONSE_3013,
        ];
    }
}
