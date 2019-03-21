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
namespace GraphAware\Bolt\Tests;

use GraphAware\Bolt\GraphDatabase;
class IntegrationTestCase extends \PHPUnit_Framework_TestCase
{
    /**
     * @var \GraphAware\Bolt\Driver
     */
    protected $driver;
    /**
     * @inheritdoc
     */
    protected function setUp()
    {
        $this->driver = GraphDatabase::driver("bolt://localhost");
    }
    /**
     * @return \GraphAware\Bolt\Driver
     */
    protected function getDriver()
    {
        return $this->driver;
    }
    /**
     * @return \Graphaware\Bolt\Protocol\SessionInterface
     */
    protected function getSession()
    {
        return $this->driver->session();
    }
    /**
     * Empty the database
     */
    protected function emptyDB()
    {
        $this->getSession()->run('MATCH (n) DETACH DELETE n');
    }
}

?>