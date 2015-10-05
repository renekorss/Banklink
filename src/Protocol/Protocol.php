<?php
/**
 * RKD Banklink
 *
 * @package Banklink\Protocol
 * @link https://github.com/renekorss/Banklink/
 * @author Rene Korss <rene.korss@gmail.com>
 * @copyright 2015 Rene Korss
 * @license MIT
 */

namespace RKD\Banklink\Protocol;

/**
 * Protocol interface
 *
 * @author Rene Korss <rene.korss@gmail.com>
 */

interface Protocol{

     /**
     * Get payment object
     *
     * @param string $orderId Order ID
     * @param float $sum Sum of order
     * @param string $message Transaction description
     * @param string $encoding Encoding
     * @param string $language Language
     * @param string $currency Currency. Default: EUR
     * @param string $timezone Timezone. Default: Europe/Tallinn
     *
     * @return RKD\Banklink\Request\PaymentRequest Payment object
     */

    function getPaymentRequest($orderId, $sum, $message, $encoding = 'UTF-8', $language = 'EST', $currency = 'EUR', $timezone = 'Europe/Tallinn');

    /**
     * Handles response from bank
     *
     * @param array $responseData Response data from bank
     * @param string $encoding Encoding
     *
     * @return RKD\Banklink\Response\PaymentResponse|RKD\Banklink\Response\AuthResponse Response object, depending on request made
     */

    function handleResponse(array $response, $encoding = 'UTF-8');

}
