<?php
/**
 * RKD Banklink.
 *
 * @link https://github.com/renekorss/Banklink/
 *
 * @author Rene Korss <rene.korss@gmail.com>
 * @copyright 2016 Rene Korss
 * @license MIT
 */
namespace RKD\Banklink\Response;

/**
 * Response wrapper.
 *
 * @author  Rene Korss <rene.korss@gmail.com>
 */
class Response
{
    /**
     * Signature verified and transaction successful.
     */
    const STATUS_SUCCESS = 1;

    /**
     * Signature not verified.
     */
    const STATUS_ERROR = -1;

    /**
     * Response status.
     *
     * @var int
     */
    protected $status;

    /**
     * Response data.
     *
     * @var array
     */
    protected $responseData;

    /**
     * Prefered language.
     *
     * @var array
     */
    protected $language;

    /**
     * Set response status and data.
     *
     * @param int   $status       Verification status
     * @param array $responseData Array of bank response
     */
    public function __construct($status, array $responseData)
    {
        $this->status = $status;
        $this->responseData = $responseData;
    }

    /**
     * Get boolean to know is transaction was successful.
     *
     * @return bool True on sucess, false othwerwise
     */
    public function wasSuccessful()
    {
        return $this->status === self::STATUS_SUCCESS;
    }

    /**
     * Get transaction status.
     *
     * @return int Status
     */
    public function getStatus()
    {
        return $this->status;
    }

    /**
     * Get response data.
     *
     * @return array Array of response
     */
    public function getResponseData()
    {
        return $this->responseData;
    }

    /**
     * Set prefered language.
     *
     * @param string $language Prefered language
     */
    public function setLanguage($language)
    {
        $this->language = $language;
    }

    /**
     * Get prefered language.
     *
     * @return string Language (EST, ENG, RUS)
     */
    public function getLanguage()
    {
        return $this->language;
    }
}
