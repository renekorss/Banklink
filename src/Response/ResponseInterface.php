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
namespace RKD\Banklink\Response;

/**
 * Response interface.
 *
 * @author Rene Korss <rene.korss@gmail.com>
 */
interface ResponseInterface
{
    /**
     * Get boolean to know is transaction was successful.
     *
     * @return bool True on sucess, false othwerwise
     */
    public function wasSuccessful() : bool;

    /**
     * Get transaction status.
     *
     * @return int Status
     */
    public function getStatus() : int;

    /**
     * Get response data.
     *
     * @return array Array of response
     */
    public function getResponseData() : array;

    /**
     * Set prefered language.
     *
     * @param string $language Prefered language
     *
     * @return self
     */
    public function setLanguage(string $language) : ResponseInterface;

    /**
     * Get prefered language.
     *
     * @return string Language (EST, ENG, RUS)
     */
    public function getLanguage() : string;
}
