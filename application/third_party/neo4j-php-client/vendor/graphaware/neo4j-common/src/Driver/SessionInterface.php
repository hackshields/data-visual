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
namespace GraphAware\Common\Driver;

use GraphAware\Common\Transaction\TransactionInterface;
interface SessionInterface
{
    /**
     * @param string      $statement
     * @param array       $parameters
     * @param null|string $tag
     *
     * @return \GraphAware\Common\Result\Result
     */
    public function run($statement, array $parameters = [], $tag = null);
    public function close();
    /**
     * @return TransactionInterface
     */
    public function transaction();
    /**
     * @param string|null $query
     * @param array       $parameters
     * @param string|null $tag
     *
     * @return PipelineInterface
     */
    public function createPipeline($query = null, array $parameters = array(), $tag = null);
}

?>