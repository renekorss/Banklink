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
 * Response wrapper.
 *
 * @author Rene Korss <rene.korss@gmail.com>
 */
class Response implements ResponseInterface
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
     * Automatic response value.
     */
    const RESPONSE_AUTO = 'Y';

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
     * @var string
     */
    protected $language;

    /**
     * Response automatic state.
     *
     * @var bool
     */
    protected $isAutomatic = false;

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
     * Get boolean to know if transaction was successful.
     *
     * @return bool True on sucess, false othwerwise
     */
    public function wasSuccessful() : bool
    {
        return $this->status === self::STATUS_SUCCESS;
    }

    /**
     * Get transaction status.
     *
     * @return int Status
     */
    public function getStatus() : int
    {
        return $this->status;
    }

    /**
     * Get response data.
     *
     * @return array Array of response
     */
    public function getResponseData() : array
    {
        return $this->responseData;
    }

    /**
     * Set prefered language.
     *
     * @param string $language Prefered language
     *
     * @return self
     */
    public function setLanguage(string $language) : ResponseInterface
    {
        $this->language = $language;
        return $this;
    }

    /**
     * Get prefered language.
     *
     * @return string Language (EST, ENG, RUS)
     */
    public function getLanguage() : string
    {
        return $this->language;
    }
}
