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

use Raven_Client;
class MockRavenClient extends Raven_Client
{
    public function capture($data, $stack, $vars = null)
    {
        $data = array_merge($this->get_user_data(), $data);
        $this->lastData = $data;
        $this->lastStack = $stack;
    }
    public $lastData;
    public $lastStack;
}

?>