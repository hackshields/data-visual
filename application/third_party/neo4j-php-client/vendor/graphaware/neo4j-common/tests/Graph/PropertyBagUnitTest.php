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
namespace GraphAware\Common\Tests\Graph;

use GraphAware\Common\Graph\PropertyBag;
use InvalidArgumentException;
use ArrayIterator;
/**
 * @group unit
 * @group graph
 */
class PropertyBagUnitTest extends \PHPUnit_Framework_TestCase
{
    public function testNewInstance()
    {
        $bag = new PropertyBag();
        $this->assertInstanceOf(PropertyBag::class, $bag);
        $this->assertFalse($bag->hasProperty('none'));
        $this->setExpectedException(InvalidArgumentException::class);
        $bag->getProperty("cool");
    }
    public function testBagWithProperties()
    {
        $bag = new PropertyBag($this->getProperties());
        $this->assertCount(2, $bag->getProperties());
        $this->assertEquals(1, $bag->getProperty("id"));
    }
    public function testSet()
    {
        $bag = new PropertyBag($this->getProperties());
        $this->assertCount(2, $bag->getProperties());
        $bag->setProperty("age", 34);
        $this->assertCount(3, $bag->getProperties());
        $this->assertEquals(34, $bag->getProperty("age"));
    }
    protected function getProperties()
    {
        return array("id" => 1, "name" => "Michael");
    }
}

?>