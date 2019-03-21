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

use GraphAware\Common\Graph\RelationshipType;
/**
 * @group unit
 * @group graph
 */
class RelationshipTypeUnitTest extends \PHPUnit_Framework_TestCase
{
    public function testInstance()
    {
        $type = RelationshipType::withName("FOLLOWS");
        $this->assertEquals("FOLLOWS", $type->getName());
        $this->assertEquals("FOLLOWS", (string) $type);
    }
}

?>