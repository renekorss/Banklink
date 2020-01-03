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
namespace RKD\Banklink\Request;

/**
 * Response interface.
 *
 * @author Rene Korss <rene.korss@gmail.com>
 */
interface RequestInterface
{

    /**
     * Get request hidden inputs.
     *
     * @return string HTML of hidden request inputs
     */
    public function getRequestInputs() : string;

    /**
     * Get request URL.
     *
     * @return string Request URL
     */
    public function getRequestUrl() : string;

    /**
     * Get request data.
     *
     * @return array Request data
     */
    public function getRequestData() : array;
}
