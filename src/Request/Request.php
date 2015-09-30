<?php
namespace RKD\Banklink\Request;

/**
 * Request class
 *
 * @author Rene Korss <rene.korss@gmail.com>
 */

abstract class Request{

    protected $requestUrl;
    protected $requestData;

    /**
     * Constructor
     *
     * @param string Request URL
     * @param array Request array
     *
     * @return void
     */

    public function __constructor($requestUrl, array $requestData){
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
