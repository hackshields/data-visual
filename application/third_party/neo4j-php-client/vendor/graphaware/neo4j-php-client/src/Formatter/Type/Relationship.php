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
/*
 * This file is part of the GraphAware Neo4j Client package.
 *
 * (c) GraphAware Limited <http://graphaware.com>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */
namespace GraphAware\Neo4j\Client\Formatter\Type;

use GraphAware\Common\Type\Relationship as RelationshipInterface;
class Relationship extends MapAccess implements RelationshipInterface
{
    /**
     * @var int
     */
    protected $id;
    /**
     * @var string
     */
    protected $type;
    /**
     * @var int
     */
    protected $startNodeIdentity;
    /**
     * @var int
     */
    protected $endNodeIdentity;
    /**
     * @param int    $id
     * @param string $type
     * @param int    $startNodeId
     * @param int    $endNodeId
     * @param array  $properties
     */
    public function __construct($id, $type, $startNodeId, $endNodeId, array $properties = [])
    {
        $this->id = $id;
        $this->type = $type;
        $this->startNodeIdentity = $startNodeId;
        $this->endNodeIdentity = $endNodeId;
        $this->properties = $properties;
    }
    /**
     * {@inheritdoc}
     */
    public function identity()
    {
        return $this->id;
    }
    /**
     * {@inheritdoc}
     */
    public function type()
    {
        return $this->type;
    }
    /**
     * {@inheritdoc}
     */
    public function hasType($type)
    {
        return $type === $this->type;
    }
    /**
     * @return int
     */
    public function startNodeIdentity()
    {
        return $this->startNodeIdentity;
    }
    /**
     * @return int
     */
    public function endNodeIdentity()
    {
        return $this->endNodeIdentity;
    }
}

?>