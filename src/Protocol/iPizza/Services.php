<?php
namespace RKD\Banklink\Protocol\iPizza;

/**
 * iPizza protocol services
 *
 * @author  Rene Korss <rene.korss@gmail.com>
 */

final class Services{

    // Payment
    const PAYMENT_REQUEST_1011     = '1011';
    const PAYMENT_REQUEST_1012     = '1012'; // Without account number and name

    const PAYMENT_RESPONSE_SUCCESS = '1111';
    const PAYMENT_RESPONSE_FAILED  = '1911';

    // Authentication
    const AUTH_REQUEST_4012        = '4012';
    const AUTH_RESPONSE_3013       = '4012';

    const AUTH_REQUEST_4011        = '4011';
    const AUTH_RESPONSE_3012       = '4012';

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
                        'VK_MSG'
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
                        'VK_MSG'
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
