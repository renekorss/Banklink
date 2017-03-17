<?php
/**
 * RKD Banklink.
 *
 * @link https://github.com/renekorss/Banklink/
 *
 * @author Rene Korss <rene.korss@gmail.com>
 * @copyright 2016-2017 Rene Korss
 * @license MIT
 */
namespace RKD\Banklink;

use RKD\Banklink\Protocol\LiiziPayment;

/**
 * Settings for Liizi payment link.
 *
 * NOTE: This class is preserved only for BC
 *
 * For more information, please visit:
 * https://klient.liisi.ee/static/payment_link_doc/
 *
 * @author  Rene Korss <rene.korss@gmail.com>
 */

class Liizi extends Liisi
{
    /**
     * Force Liizi class to use LiiziPayment protocol.
     *
     * @param RKD\Banklink\Protocol\LiiziPayment $protocol   Protocol used
     */
    public function __construct(LiiziPayment $protocol)
    {
        parent::__construct($protocol);
    }
}
