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
/*
 * This file is part of the Predis package.
 *
 * (c) Daniele Alessandri <suppakilla@gmail.com>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */
namespace Predis\Command;

/**
 * @link http://redis.io/commands/sunionstore
 *
 * @author Daniele Alessandri <suppakilla@gmail.com>
 */
class SetUnionStore extends SetIntersectionStore
{
    /**
     * {@inheritdoc}
     */
    public function getId()
    {
        return 'SUNIONSTORE';
    }
}

?>