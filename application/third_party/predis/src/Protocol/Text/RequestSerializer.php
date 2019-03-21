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
namespace Predis\Protocol\Text;

use Predis\Command\CommandInterface;
use Predis\Protocol\RequestSerializerInterface;
/**
 * Request serializer for the standard Redis wire protocol.
 *
 * @link http://redis.io/topics/protocol
 *
 * @author Daniele Alessandri <suppakilla@gmail.com>
 */
class RequestSerializer implements RequestSerializerInterface
{
    /**
     * {@inheritdoc}
     */
    public function serialize(CommandInterface $command)
    {
        $commandID = $command->getId();
        $arguments = $command->getArguments();
        $cmdlen = strlen($commandID);
        $reqlen = count($arguments) + 1;
        $buffer = "*{$reqlen}\r\n\${$cmdlen}\r\n{$commandID}\r\n";
        foreach ($arguments as $argument) {
            $arglen = strlen($argument);
            $buffer .= "\${$arglen}\r\n{$argument}\r\n";
        }
        return $buffer;
    }
}

?>