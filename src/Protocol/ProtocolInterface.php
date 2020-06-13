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
        string $language = 'EST',
        string $currency = 'EUR',
        array $customRequestData = [],
        string $encoding = 'UTF-8',
        string $timezone = 'Europe/Tallinn'
    ) : array;

    /**
     * Get authentication object
     *
     * @param string|null $recId    Bank identifier
     * @param string|null $nonce    Random nonce
     * @param string|null $rid      Session identifier.
     * @param string      $encoding Encoding
     * @param string      $language Language
     * @param string      $timezone Timezone. Default: Europe/Tallinn
     *
     * @return array Authentication request data
     */
    public function getAuthRequest(
        ?string $recId = null,
        ?string $nonce = null,
        ?string $rid = null,
        string $encoding = 'UTF-8',
        string $language = 'EST',
        string $timezone = 'Europe/Tallinn'
    ) : array;

    /**
     * Handles response from bank.
     *
     * @param array  $response Response data from bank
     * @param string $encoding     Encoding
     *
     * @return RKD\Banklink\Response\ResponseInterface Response object, depending on request made
     */
    public function handleResponse(array $response, string $encoding = 'UTF-8') : ResponseInterface;
}
