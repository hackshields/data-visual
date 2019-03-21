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
 * This file is part of the Monolog package.
 *
 * (c) Jordi Boggiano <j.boggiano@seld.be>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */
namespace Monolog\Handler;

use Monolog\Logger;
/**
 * Blackhole
 *
 * Any record it can handle will be thrown away. This can be used
 * to put on top of an existing stack to override it temporarily.
 *
 * @author Jordi Boggiano <j.boggiano@seld.be>
 */
class NullHandler extends AbstractHandler
{
    /**
     * @param int $level The minimum logging level at which this handler will be triggered
     */
    public function __construct($level = Logger::DEBUG)
    {
        parent::__construct($level, false);
    }
    /**
     * {@inheritdoc}
     */
    public function handle(array $record)
    {
        if ($record['level'] < $this->level) {
            return false;
        }
        return true;
    }
}

?>