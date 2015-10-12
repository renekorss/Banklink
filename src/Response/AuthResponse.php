<?php
/**
 * RKD Banklink
 *
 * @package Banklink\Response
 * @link https://github.com/renekorss/Banklink/
 * @author Rene Korss <rene.korss@gmail.com>
 * @copyright 2015 Rene Korss
 * @license MIT
 */

namespace RKD\Banklink\Response;

/**
 * Authentication response wrapper
 *
 * @author  Rene Korss <rene.korss@gmail.com>
 */

class AuthResponse extends Response{

    /**
     * Authentication methods mapping with token value
     * token => human readable auth method
     * @var array
     */
    private static $authMethods = array(
            1 => 'ID card',
            2 => 'Mobile ID',
            5 => 'One-off code card',
            6 => 'PIN-calculator',
            7 => 'Code card'
        );

    /**
     * User id (personal code)
     * @var int
     */
    protected $userId;

    /**
     * User name
     * @var string
     */
    protected $userName;

    /**
     * Country
     * @var string
     */
    protected $country;

    /**
     * Token
     * @var int
     */
    protected $token;

    /**
     * RID
     * @var string
     */
    protected $rid;

    /**
     * Nonce
     * @var string
     */
    protected $nonce;

    /**
     * Authentication date
     * @var string
     */
    protected $authDate;

    /**
     * Set user id
     *
     * @param int $userId User id
     */

    public function setUserId($userId){
        $this->userId = $userId;
    }

    /**
     * Get user id
     *
     * @return int User id
     */

    public function getUserId(){
        return $this->userId;
    }

    /**
     * Set user name
     *
     * @param string $userName User name
     */

    public function setUserName($userName){
        $this->userName = $userName;
    }

    /**
     * Get user name
     *
     * @return string User name
     */

    public function getUserName(){
        return $this->userName;
    }

    /**
     * Set user country
     *
     * @param string $country Country
     */

    public function setUserCountry($country){
        $this->userCountry = $country;
    }

    /**
     * Get user country
     *
     * @return string Country
     */

    public function getUserCountry(){
        return $this->userCountry;
    }

    /**
     * Set token
     *
     * @param string $token Token (VK_TOKEN)
     */

    public function setToken($token){
        $this->token = $token;
    }

    /**
     * Get token
     *
     * @return string Token
     */

    public function getToken(){
        return $this->token;
    }

    /**
     * Set nonce
     *
     * @param string $nonce Nonce (VK_NONCE)
     */

    public function setNonce($nonce){
        $this->nonce = $nonce;
    }

    /**
     * Get nonce
     *
     * @return string Nonce
     */

    public function getNonce(){
        return $this->nonce;
    }

    /**
     * Set rid
     *
     * @param string $rid Rid (VK_RID)
     */

    public function setRid($rid){
        $this->rid = $rid;
    }

    /**
     * Get rid
     *
     * @return string Rid
     */

    public function getRid(){
        return $this->rid;
    }

   /**
     * Set auth date
     *
     * @param string $authDate Auth date
     */

    public function setAuthDate($authDate){
        $this->authDate = $authDate;
    }

    /**
     * Get auth date
     *
     * @return string Auth date
     */

    public function getAuthDate(){
        return $this->authDate;
    }

    /**
     * Get auth method
     *
     * @return string Authentication method
     */

    public function getAuthMethod(){

        $authMethod = 'unknown';

        if((int)$this->token > 0 && in_array($this->token, array_keys(self::$authMethods))){
            $authMethod = self::$authMethods[$this->token];
        }

        return $authMethod;
    }

}
