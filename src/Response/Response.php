<?php
namespace RKD\Banklink\Response;


/**
 * Response wrapper
 *
 * @author  Rene Korss <rene.korss@gmail.com>
 */

class Response{

    /**
     * Signature verified and transaction successful
     */
    const STATUS_SUCCESS = 1;

    /**
     * Signature not verified
     */

    const STATUS_ERROR   = -1;

    protected $status;
    protected $responseData;

    /**
     * @param integer Verification status
     * @param array Array of bank response
     *
     * @return void
     */

    public function __construct($status, array $responseData){
        $this->status       = $status;
        $this->responseData = $responseData;
    }

    /**
     * Get boolean to know is transaction was successful
     *
     * @return boolean True on sucess, false othweriws
     */

    public function isSuccessful(){
        return $this->status === self::STATUS_SUCCESS;
    }

    /**
     * Get transaction status
     *
     * @return boolean True on sucess, false otherwise
     */

    public function getStatus(){
        return $this->status;
    }

    /**
     * Get response data
     *
     * @return array TArray of response
     */

    public function getResponseData(){
        return $this->responseData;
    }
}
