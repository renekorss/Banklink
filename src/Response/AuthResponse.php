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
 * Authentication response wrapper.
 *
 * @author Rene Korss <rene.korss@gmail.com>
 */
class AuthResponse extends Response
{
    /**
     * Authentication methods mapping with token value
     * token => human readable auth method.
     *
     * @var array
     */
    private static $authMethods = [
            1 => 'ID card',
            2 => 'Mobile ID',
            5 => 'One-off code card',
            6 => 'PIN-calculator',
            7 => 'Code card',
        ];

    /**
     * User id (personal code).
     *
     * @var string
     */
    protected $userId;

    /**
     * User name.
     *
     * @var string
     */
    protected $userName;

    /**
     * Country.
     *
     * @var string
     */
    protected $country;

    /**
     * Token.
     *
     * @var int
     */
    protected $token;

    /**
     * RID.
     *
     * @var string
     */
    protected $rid;

    /**
     * Nonce.
     *
     * @var string
     */
    protected $nonce;

    /**
     * Authentication date.
     *
     * @var string
     */
    protected $authDate;

    /**
     * Set user id.
     *
     * @param string $userId User id
     *
     * @return self
     */
    public function setUserId(string $userId) : self
    {
        $this->userId = $userId;
        return $this;
    }

    /**
     * Get user id.
     *
     * @return string User id
     */
    public function getUserId() : ?string
    {
        return $this->userId;
    }

    /**
     * Set user name.
     *
     * @param string $userName User name
     */
    public function setUserName(string $userName) : self
    {
        $this->userName = $userName;
        return $this;
    }

    /**
     * Get user name.
     *
     * @return string User name
     */
    public function getUserName() : ?string
    {
        return $this->userName;
    }

    /**
     * Set user country.
     *
     * @param string $country Country
     */
    public function setUserCountry(string $country) : self
    {
        $this->userCountry = $country;
        return $this;
    }

    /**
     * Get user country.
     *
     * @return string Country
     */
    public function getUserCountry() : ?string
    {
        return $this->userCountry;
    }

    /**
     * Set token.
     *
     * @param string $token Token (VK_TOKEN)
     *
     * @return self
     */
    public function setToken(string $token) : ?self
    {
        $this->token = $token;
        return $this;
    }

    /**
     * Get token.
     *
     * @return string Token
     */
    public function getToken() : string
    {
        return $this->token;
    }

    /**
     * Set nonce.
     *
     * @param string $nonce Nonce (VK_NONCE)
     *
     * @return self
     */
    public function setNonce(string $nonce) : self
    {
        $this->nonce = $nonce;
        return $this;
    }

    /**
     * Get nonce.
     *
     * @return string Nonce
     */
    public function getNonce() : ?string
    {
        return $this->nonce;
    }

    /**
     * Set rid.
     *
     * @param string $rid Rid (VK_RID)
     *
     * @return self
     */
    public function setRid(string $rid) : self
    {
        $this->rid = $rid;
        return $this;
    }

    /**
     * Get rid.
     *
     * @return string Rid
     */
    public function getRid() : ?string
    {
        return $this->rid;
    }

    /**
     * Set auth date.
     *
     * @param string $authDate Auth date
     *
     * @return self
     */
    public function setAuthDate(string $authDate) : self
    {
        $this->authDate = $authDate;
        return $this;
    }

    /**
     * Get auth date.
     *
     * @return string Auth date
     */
    public function getAuthDate() : ?string
    {
        return $this->authDate;
    }

    /**
     * Get human readable auth method.
     *
     * @return string Authentication method
     */
    public function getAuthMethod() : string
    {
        $authMethod = 'unknown';

        if ((int) $this->token > 0 && in_array($this->token, array_keys(self::$authMethods))) {
            $authMethod = self::$authMethods[$this->token];
        }

        return $authMethod;
    }
}
