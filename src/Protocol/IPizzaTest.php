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
namespace RKD\Banklink\Protocol;

/**
 * Protocol for IPizza based banklinks.
 *
 * @author Rene Korss <rene.korss@gmail.com>
 */
class IPizzaTest extends IPizza
{
    public function __construct(
        $sellerId,
        $privateKey,
        $privateKeyPassword,
        $publicKey,
        $requestUrl,
        $sellerName = null,
        $sellerAccount = null,
        $version = '008'
    ) {
        parent::__construct(
            $sellerId,
            $privateKey,
            $privateKeyPassword,
            $publicKey,
            $requestUrl,
            $sellerName,
            $sellerAccount,
            $version
        );
    }

    /**
     * Validate bank signature.
     *
     * @param array  $response Array of VK_* fields
     * @param string $encoding Encoding
     *
     * @return bool True on success, false otherwise
     */
    protected function validateSignature(array $response, $encoding = 'UTF-8'): bool
    {
        $this->result = 1;
        return $this->result === 1;
    }
}
