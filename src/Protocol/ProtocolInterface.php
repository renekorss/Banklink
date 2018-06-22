<?php
/**
 * RKD Banklink.
 *
 * @link https://github.com/renekorss/Banklink/
 *
 * @author Rene Korss <rene.korss@gmail.com>
 * @copyright 2016-2018 Rene Korss
 * @license MIT
 */
namespace RKD\Banklink\Protocol;

use RKD\Banklink\Response\ResponseInterface;

/**
 * Protocol interface.
 *
 * @author Rene Korss <rene.korss@gmail.com>
 */
interface ProtocolInterface
{
    /**
     * Get payment object.
     *
     * @param string $orderId  Order ID
     * @param float  $sum      Sum of order
     * @param string $message  Transaction description
     * @param string $language Language
     * @param string $currency Currency. Default: EUR
     * @param array  $customRequestData Optional custom request data
     * @param string $encoding Encoding
     * @param string $timezone Timezone. Default: Europe/Tallinn
     *
     * @return array Payment request data
     */
    public function getPaymentRequest(
        $orderId,
        $sum,
        $message,
        $language = 'EST',
        $currency = 'EUR',
        $customRequestData = [],
        $encoding = 'UTF-8',
        $timezone = 'Europe/Tallinn'
    ) : array;

    /**
     * Get authetication object.
     *
     * @param string $recId Bank identifier
     * @param string $nonce Random nonce
     * @param string $rid Session identifier.
     * @param string $encoding Encoding
     * @param string $language Language
     * @param string $timezone Timezone. Default: Europe/Tallinn
     *
     * @return array Authentication request data
     */
    public function getAuthRequest(
        $recId = null,
        $nonce = null,
        $rid = null,
        $encoding = 'UTF-8',
        $language = 'EST',
        $timezone = 'Europe/Tallinn'
    ) : array;

    /**
     * Handles response from bank.
     *
     * @param array  $response Response data from bank
     * @param string $encoding     Encoding
     *
     * @return RKD\Banklink\Response\ResponseInterface Response object, depending on request made
     */
    public function handleResponse(array $response, $encoding = 'UTF-8') : ResponseInterface;
}
