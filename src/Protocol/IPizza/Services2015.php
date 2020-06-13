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
 * IPizza 2015 protocol services.
 *
 * @author Rene Korss <rene.korss@gmail.com>
 */
final class Services2015
{
    // Payment
    /**
     * Request 1001.
     *
     * @var string
     */
    const PAYMENT_REQUEST_1001 = '1001';

    /**
     * Request 1002.
     *
     * @var string
     */
    const PAYMENT_REQUEST_1002 = '1002';

    /**
     * Request 2001.
     *
     * @var string
     */
    const PAYMENT_REQUEST_2001 = '2001';

    /**
     * Successful response service.
     *
     * @var string
     */
    const PAYMENT_RESPONSE_SUCCESS = '1101';

    /**
     * Successful but not executed response service
     *
     * @var string
     */
    const PAYMENT_RESPONSE_ERROR = '1201';

    /**
     * Failed response service
     *
     * @var string
     */
    const PAYMENT_RESPONSE_FAILED = '1901';

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
            case self::PAYMENT_REQUEST_1001:
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
                    'VK_RETURN'
                ];
                break;
            case self::PAYMENT_REQUEST_1002:
                return [
                    'VK_SERVICE',
                    'VK_VERSION',
                    'VK_SND_ID',
                    'VK_STAMP',
                    'VK_AMOUNT',
                    'VK_CURR',
                    'VK_REF',
                    'VK_MSG',
                    'VK_RETURN'
                ];
                break;
            case self::PAYMENT_REQUEST_2001:
                return [
                    'VK_SERVICE',
                    'VK_VERSION',
                    'VK_SND_ID',
                    'VK_STAMP',
                    'VK_AMOUNT',
                    'VK_CURR',
                    'VK_ACC',
                    'VK_PANK',
                    'VK_NAME',
                    'VK_REF',
                    'VK_MSG',
                    'VK_RETURN'
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
                    'VK_T_DATE',
                ];
                break;
            case self::PAYMENT_RESPONSE_ERROR:
                return [
                    'VK_SERVICE',
                    'VK_VERSION',
                    'VK_SND_ID',
                    'VK_REC_ID',
                    'VK_STAMP',
                    'VK_AMOUNT',
                    'VK_CURR',
                    'VK_REC_ACC',
                    'VK_REC_NAME',
                    'VK_SND_ACC',
                    'VK_SND_NAME',
                    'VK_REF',
                    'VK_MSG'
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
            default:
                throw new UnexpectedValueException(sprintf('Service %s is not supported.', $serviceId));
                break;
        }
    }
}
