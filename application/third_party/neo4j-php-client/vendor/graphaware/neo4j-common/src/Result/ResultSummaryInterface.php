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
namespace GraphAware\Common\Result;

use GraphAware\Common\Cypher\StatementInterface;
use GraphAware\Common\Cypher\StatementType;
interface ResultSummaryInterface
{
    /**
     * @param \GraphAware\Common\Cypher\StatementInterface $statement
     */
    public function __construct(StatementInterface $statement);
    /**
     * @return \GraphAware\Common\Cypher\StatementInterface
     */
    public function statement();
    /**
     * @return StatementStatistics
     */
    public function updateStatistics();
    /**
     * @return array
     */
    public function notifications();
    /**
     * @return StatementType
     */
    public function statementType();
}

?>