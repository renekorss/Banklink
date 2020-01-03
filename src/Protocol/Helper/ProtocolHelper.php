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
     */
    public static function calculateReference(int $orderId) : string
    {
        $length = strlen($orderId);

        $orderId = (string) $orderId;
        $multipliers = [7, 3, 1];
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

    /**
     * Convert ISO-639-2 language code to ISO-639-1
     *
     * @param string $language ISO-639-2 language code
     *
     * @return string ISO-639-1 language code
     */
    public static function langToISO6391(string $language) : string
    {
        $languages = [
            'eng' => 'en',
            'est' => 'et',
            'rus' => 'ru',
            'lat' => 'lv',
            'lit' => 'lt',
            'fin' => 'fi',
            // Germany has two possible values
            'ger' => 'de',
            'deu' => 'de',
        ];

        $language = strtolower($language);

        if (array_key_exists($language, $languages)) {
            return $languages[$language];
        }

        return 'en'; // @codeCoverageIgnore
    }

    /**
     * Generates transaction number for ECommerce protocol
     *
     * Format: YYYYMM + rand between 100 000 - 999 999
     *
     * @return string Random ecuno
     */
    public static function generateEcuno() : string
    {
        $rnd   = rand(100000, 999999);
        $date  = date("Ym");
        $ecuno = $date . $rnd;

        return $ecuno;
    }

    /**
     * Multibyte str_pad
     *
     * @param mixed $input Input value
     * @param int $padLength Desired length
     * @param string $padString Pad with
     * @param int $padType Pad direction
     * @param string $encoding Encoding
     *
     * @return string Padded string
     */

    public static function mbStrPad($input, int $padLength, string $padString = ' ', int $padType = STR_PAD_RIGHT, ?string $encoding = null) : string
    {
        if (is_null($input) || strlen($input) === 0) {
            $input = '';
        }

        $diff = strlen($input) - mb_strlen($input);

        if ($encoding) {
            $diff = strlen($input) - mb_strlen($input, $encoding);
        }

        return str_pad($input, $padLength + $diff, $padString, $padType);
    }
}
