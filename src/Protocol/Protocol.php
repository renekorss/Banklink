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
namespace RKD\Banklink\Protocol;

/**
 * Protocol interface.
 *
 * @author Rene Korss <rene.korss@gmail.com>
 */
interface Protocol
{
    /**
     * Get payment object.
     *
     * @param string $orderId  Order ID
     * @param float  $sum      Sum of order
     * @param string $message  Transaction description
     * @param string $encoding Encoding
     * @param string $language Language
     * @param string $currency Currency. Default: EUR
     * @param string $timezone Timezone. Default: Europe/Tallinn
     *
     * @return RKD\Banklink\Request\PaymentRequest Payment object
     */
    public function getPaymentRequest(
        $orderId,
        $sum,
        $message,
        $encoding = 'UTF-8',
        $language = 'EST',
        $currency = 'EUR',
        $timezone = 'Europe/Tallinn'
    );

    /**
     * Handles response from bank.
     *
     * @param array  $responseData Response data from bank
     * @param string $encoding     Encoding
     *
     * @return \Response\PaymentResponse|\Response\AuthResponse Response object, depending on request made
     */
    public function handleResponse(array $response, $encoding = 'UTF-8');
}
