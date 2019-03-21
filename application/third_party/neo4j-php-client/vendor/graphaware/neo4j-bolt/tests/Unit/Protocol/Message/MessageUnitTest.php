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
namespace GraphAware\Bolt\Tests\Protocol\Message;

use GraphAware\Bolt\Protocol\Message\SuccessMessage;
use GraphAware\Bolt\Protocol\Message\AbstractMessage;
/**
 * Class MessageUnitTest
 * @package GraphAware\Bolt\Tests\Protocol\Message
 *
 * @group message
 * @group unit
 */
class MessageUnitTest extends \PHPUnit_Framework_TestCase
{
    public function testSuccessMessageWithoutFields()
    {
        $message = new SuccessMessage(array());
        $this->assertInstanceOf(AbstractMessage::class, $message);
    }
}

?>