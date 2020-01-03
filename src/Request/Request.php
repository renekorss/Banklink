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

use UnexpectedValueException;

/**
 * Abstract request class.
 *
 * @author Rene Korss <rene.korss@gmail.com>
 */
abstract class Request implements RequestInterface
{
    /**
     * Request url.
     *
     * @var string
     */
    protected $requestUrl;

    /**
     * Request data.
     *
     * @var array
     */
    protected $requestData;

    /**
     * Constructor.
     *
     * @param string $requestUrl  Request URL
     * @param array  $requestData Request array
     */
    public function __construct(string $requestUrl, array $requestData)
    {
        $this->requestUrl = $requestUrl;
        $this->requestData = $requestData;
    }

    /**
     * Get request hidden inputs.
     *
     * @return string HTML of hidden request inputs
     */
    public function getRequestInputs() : string
    {
        if (empty($this->requestData)) {
            throw new UnexpectedValueException('Can\'t generate inputs. Request data is empty.');
        }

        $html = '';

        foreach ($this->requestData as $key => $value) {
            $html .= vsprintf(
                '<input type="hidden" id="%s" name="%s" value="%s" />',
                [strtolower($key), $key, $value]
            )."\n";
        }

        return $html;
    }

    /**
     * Get request URL.
     *
     * @return string Request URL
     */
    public function getRequestUrl() : string
    {
        return $this->requestUrl;
    }

    /**
     * Get request data.
     *
     * @return array Request data
     */
    public function getRequestData() : array
    {
        return $this->requestData;
    }
}
