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

interface StatementStatisticsInterface
{
    /**
     * @return bool
     */
    public function containsUpdates();
    /**
     * @return int
     */
    public function nodesCreated();
    /**
     * @return int
     */
    public function nodesDeleted();
    /**
     * @return int
     */
    public function relationshipsCreated();
    /**
     * @return int
     */
    public function relationshipsDeleted();
    /**
     * @return int
     */
    public function propertiesSet();
    /**
     * @return int
     */
    public function labelsAdded();
    /**
     * @return int
     */
    public function labelsRemoved();
    /**
     * @return int
     */
    public function indexesAdded();
    /**
     * @return int
     */
    public function indexesRemoved();
    /**
     * @return int
     */
    public function constraintsAdded();
    /**
     * @return int
     */
    public function constraintsRemoved();
}

?>