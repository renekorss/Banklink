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
namespace RKD\Banklink\Protocol\Helper;

/**
 * Protocol helper.
 *
 * Adds methdods used by multiple protocols. Such as calculating reference etc.
 *
 * @author Rene Korss <rene.korss@gmail.com>
 */
class ProtocolHelper
{
    /**
     * Calculate reference number with 7-3-1 method.
     *
     * See ENG: http://www.pangaliit.ee/en/settlements-and-standards/reference-number-of-the-invoice
     *     EST: http://www.pangaliit.ee/et/arveldused/7-3-1meetod
     *
     * @param int $orderId Order ID
     *
     * @return string Calculated reference
     *
     * @throws InvalidArgumentException If order ID is not in correct length
     */
    public static function calculateReference($orderId)
    {
        $length = strlen($orderId);

        if ($length > 19) {
            throw new \InvalidArgumentException('Order id can be up to 19 digits long.');
        }

        // This makes sure that order id length is at least 1 digit
        if (!is_int($orderId)) {
            throw new \InvalidArgumentException('Order id must be integer.');
        }

        $orderId = (string) $orderId;
        $multipliers = array(7, 3, 1);
        $total = 0;
        $multiplierKey = 0;

        for ($i = $length - 1; $i >= 0; --$i) {
            $total += (int) $orderId[$i] * $multipliers[$multiplierKey];
            $multiplierKey = $multiplierKey < 2 ? ++$multiplierKey : 0;
        };

        $closestTen = ceil($total / 10) * 10;
        $checkNum = $closestTen - $total;

        return $orderId.$checkNum;
    }
}
