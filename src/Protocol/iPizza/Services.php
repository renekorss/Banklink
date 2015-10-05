<?php
/**
 * RKD Banklink
 *
 * @package Banklink\Protocol\iPizza
 * @link https://github.com/renekorss/Banklink/
 * @author Rene Korss <rene.korss@gmail.com>
 * @copyright 2015 Rene Korss
 * @license MIT
 */

namespace RKD\Banklink\Protocol\iPizza;

/**
 * iPizza protocol services
 *
 * @author  Rene Korss <rene.korss@gmail.com>
 */

final class Services{

    // Payment
    /**
     * Request 1011
     * @var string
     */
    const PAYMENT_REQUEST_1011     = '1011';

    /**
     * Request 1012
     * Dosen't require name and account number
     * @var string
     */
    const PAYMENT_REQUEST_1012     = '1012';

    /**
     * Successful response service
     * @var string
     */
    const PAYMENT_RESPONSE_SUCCESS = '1111';

    /**
     * Failed response service
     * Dosen't require name and account number
     * @var string
     */
    const PAYMENT_RESPONSE_FAILED  = '1911';

    // Authentication
    /**
     * Request 4012
     * @var string
     */
    const AUTH_REQUEST_4012        = '4012';

    /**
     * Response 3013
     * @var string
     */
    const AUTH_RESPONSE_3013       = '3013';

    /**
     * Request 4011
     * @var string
     */
    const AUTH_REQUEST_4011        = '4011';

    /**
     * Response 3012
     * @var string
     */
    const AUTH_RESPONSE_3012       = '3012';

    /**
     * Get fields required for service
     *
     * @param string $serviceId Service number
     *
     * @return array Array of fields for service
     */

    public static function getFields($serviceId){

        switch ($serviceId) {
            case self::PAYMENT_REQUEST_1011:
                    return array(
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
                        'VK_DATETIME'
                    );
                break;
            case self::PAYMENT_REQUEST_1012:
                    return array(
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
                        'VK_DATETIME'
                    );
                break;
            case self::PAYMENT_RESPONSE_SUCCESS:
                    return array(
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
                        'VK_T_DATETIME'
                    );
                break;
            case self::PAYMENT_RESPONSE_FAILED:
                    return array(
                        'VK_SERVICE',
                        'VK_VERSION',
                        'VK_SND_ID',
                        'VK_REC_ID',
                        'VK_STAMP',
                        'VK_REF',
                        'VK_MSG'
                    );
                break;
            default:
                throw new \UnexpectedValueException(sprintf('Service %s is not supported.', $serviceId));
                break;
        }
    }

    /**
     * Get supported payment response services
     *
     * @return array Array of payments services ID-s
     */

    public static function getPaymentResponseServices(){
        return array(
            self::PAYMENT_RESPONSE_SUCCESS,
            self::PAYMENT_RESPONSE_FAILED
        );
    }

    /**
     * Get supported authentication response services
     *
     * @return array Array of payments services ID-s
     */

    public static function getAuthenticationResponseServices(){
        return array(
            self::AUTH_RESPONSE_3012,
            self::AUTH_RESPONSE_3013
        );
    }

}
