<?php
namespace RKD\Banklink\Protocol;

/**
 * Protocol interface
 *
 * @author Rene Korss <rene.korss@gmail.com>
 */

interface Protocol{

    function getPaymentRequestData($orderId, $sum, $message, $encoding = 'UTF-8', $language = 'EST', $currency = 'EUR', $timezone = 'Europe/Tallinn');

    function handleResponse(array $response, $encoding = 'UTF-8');

}
