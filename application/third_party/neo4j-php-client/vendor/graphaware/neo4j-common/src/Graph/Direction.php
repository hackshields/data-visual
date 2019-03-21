<?php
/*
 * @ PHP 5.6
 * @ Decoder version : 1.0.0.1
 * @ Release on : 24.03.2018
 * @ Website    : http://EasyToYou.eu
 */

/*
 * @ PHP 5.6
 * @ Decoder version : 1.0.0.1
 * @ Release on : 24.03.2018
 * @ Website    : http://EasyToYou.eu
 */
/**
 * This file is part of the GraphAware Neo4j Common package.
 *
 * (c) GraphAware Limited <http://graphaware.com>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */
namespace GraphAware\Common\Graph;

use MyCLabs\Enum\Enum;
class Direction extends Enum
{
    const INCOMING = 'INCOMING';
    const OUTGOING = 'OUTGOING';
    const BOTH = 'BOTH';
    /**
     * @return Direction
     */
    public static function INCOMING()
    {
        return new self(self::INCOMING);
    }
    /**
     * @return Direction
     */
    public static function OUTGOING()
    {
        return new self(self::OUTGOING);
    }
    /**
     * @return Direction
     */
    public static function BOTH()
    {
        return new self(self::BOTH);
    }
}

?>