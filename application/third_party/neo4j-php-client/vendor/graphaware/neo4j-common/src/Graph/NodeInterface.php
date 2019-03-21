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
namespace GraphAware\Common\Graph;

interface NodeInterface extends PropertyBagInterface
{
    /**
     * Returns the node labels.
     *
     * @return Label[]
     */
    public function getLabels();
    /**
     * Returns whether or not the node has the given <code>$name</code> label.
     *
     * @param string $name
     *
     * @return bool
     */
    public function hasLabel($name);
    /**
     * Returns the node internal id.
     *
     * @return int
     */
    public function getId();
    /**
     * Returns the node relationships.
     *
     * @return RelationshipInterface[]
     */
    public function getRelationships();
    /**
     * Returns whether or not the node has relationships.
     *
     * @return bool
     */
    public function hasRelationships();
    /**
     * Add a relationship to the Node.
     * 
     * @param RelationshipInterface $relationship
     */
    public function addRelationship(RelationshipInterface $relationship);
}

?>