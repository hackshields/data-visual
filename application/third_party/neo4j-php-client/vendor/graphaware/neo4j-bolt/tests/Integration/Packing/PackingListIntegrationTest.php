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
namespace GraphAware\Bolt\Tests\Integration\Packing;

use GraphAware\Bolt\Tests\IntegrationTestCase;
class PackingListIntegrationTest extends IntegrationTestCase
{
    protected function setUp()
    {
        parent::setUp();
        $this->emptyDB();
    }
    public function testPackingList32()
    {
        $session = $this->getSession();
        $session->run('UNWIND range(1, 40000) AS i CREATE (n:TestList {id: i})');
        $result = $session->run('MATCH (n:TestList) RETURN collect(n.id) AS list');
        $this->assertCount(40000, $result->firstRecord()->get('list'));
    }
}

?>