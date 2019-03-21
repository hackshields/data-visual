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
namespace GraphAware\Common\Tests\Collection;

use GraphAware\Common\Collection\ArrayList;
use GraphAware\Common\Collection\Map;
use GraphAware\Common\Collections;
class CollectionsTest extends \PHPUnit_Framework_TestCase
{
    public function testListCreation()
    {
        $listcoll = Collections::asList([1, 2, 3]);
        $this->assertInstanceOf(ArrayList::class, $listcoll);
        $this->assertCount(3, $listcoll->getElements());
    }
    public function testMapCreation()
    {
        $mapcoll = Collections::asMap(['hello' => 'you']);
        $this->assertInstanceOf(Map::class, $mapcoll);
        $this->assertCount(1, $mapcoll->getElements());
    }
}

?>