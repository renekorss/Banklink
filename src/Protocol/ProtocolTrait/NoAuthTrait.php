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
namespace RKD\Banklink\Protocol\ProtocolTrait;

use RKD\Banklink\Response\ResponseInterface;

/**
 * Trait for protocols not using auth request.
 *
 * @author Rene Korss <rene.korss@gmail.com>
 */
trait NoAuthTrait
{
    /**
     * Get authentication object.
     *
     * @param string $recId Bank identifier
     * @param string $nonce Random nonce
     * @param string $rid Session identifier.
     * @param string $encoding Encoding
     * @param string $language Language
     * @param string $timezone Timezone. Default: Europe/Tallinn
     *
     * @return array Authentication request data
     *
     * @SuppressWarnings("unused")
     */
    public function getAuthRequest(
        $recId = null,
        $nonce = null,
        $rid = null,
        $encoding = 'UTF-8',
        $language = 'EST',
        $timezone = 'Europe/Tallinn'
    ) : array {
        throw new \LogicException(get_class().' protocol dosen\'t support authentication.');
    }

    /**
     * Handles response from bank.
     *
     * @param array  $response Response data from bank
     * @param string $encoding Encoding
     *
     * @return \Response\PaymentResponse Payment response object
     */
    public function handleResponse(array $response, $encoding = 'UTF-8') : ResponseInterface
    {
        $success = $this->validateSignature($response, $encoding);
        return $this->handlePaymentResponse($response, $success);
    } // @codeCoverageIgnore
}
