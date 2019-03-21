<?php
/*
 * @ PHP 5.6
 * @ Decoder version : 1.0.0.1
 * @ Release on : 24.03.2018
 * @ Website    : http://EasyToYou.eu
 */

/*
 * This file is part of the Symfony package.
 *
 * (c) Fabien Potencier <fabien@symfony.com>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */
namespace Symfony\Component\Serializer\Tests\Fixtures;

class NotSerializedConstructorArgumentDummy
{
    private $bar;
    public function __construct($foo)
    {
    }
    public function getBar()
    {
        return $this->bar;
    }
    public function setBar($bar)
    {
        $this->bar = $bar;
    }
}

?>