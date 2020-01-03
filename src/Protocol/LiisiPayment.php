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
namespace RKD\Banklink\Protocol;

use RKD\Banklink\Protocol\ProtocolTrait\NoAuthTrait;

/**
 * Protocol for Liisi payment link.
 * Same as IPizza, but without authentication
 *
 * @author Rene Korss <rene.korss@gmail.com>
 */
class LiisiPayment extends IPizza
{
    // No authentication for this protocol
    use NoAuthTrait;
}
