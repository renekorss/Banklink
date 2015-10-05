<?php
/**
 * RKD Banklink
 *
 * @package Banklink\Request
 * @link https://github.com/renekorss/Banklink/
 * @author Rene Korss <rene.korss@gmail.com>
 * @copyright 2015 Rene Korss
 * @license MIT
 */

namespace RKD\Banklink\Request;

/**
 * Abstract request class
 *
 * @author Rene Korss <rene.korss@gmail.com>
 */

abstract class Request{

    /**
     * Request url
     * @var string
     */
    protected $requestUrl;

    /**
     * Request data
     * @var array
     */
    protected $requestData;

    /**
     * Constructor
     *
     * @param string $requestUrl Request URL
     * @param array $requestData Request array
     *
     * @return void
     */

    public function __construct($requestUrl, array $requestData){
        $this->requestUrl  = $requestUrl;
        $this->requestData = $requestData;
    }

    /**
     * Get request hidden inputs
     *
     * @return string HTML of hidden request inputs
     */

    public function getRequestInputs(){

        if(empty($this->requestData)){
            throw new \UnexpectedValueException('Can\'t generate inputs. Request data is empty.');
        }

        $html = '';

        foreach ($this->requestData as $key => $value) {
            $html .= vsprintf('<input type="hidden" id="%s" name="%s" value="%s" />', array(strtolower($key), $key, $value))."\n";
        }

        return $html;
    }

    /**
     * Get request URL
     *
     * @return string Request URL
     */

    public function getRequestUrl(){
        return $this->requestUrl;
    }

    /**
     * Get request data
     *
     * @return array Request data
     */

    public function getRequestData(){
        return $this->requestData;
    }

}
