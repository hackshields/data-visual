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
 * This file is part of the GraphAware Bolt package.
 *
 * (c) Graph Aware Limited <http://graphaware.com>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */
namespace GraphAware\Bolt\Result\Type;

use GraphAware\Common\Type\Relationship as BaseRelationshipInterface;
class UnboundRelationship extends MapAccess implements BaseRelationshipInterface
{
    /**
     * @var string
     */
    protected $identity;
    /**
     * @var string
     */
    protected $type;
    /**
     * @param string       $identity
     * @param string       $type
     * @param string array $properties
     */
    public function __construct($identity, $type, array $properties)
    {
        $this->identity = $identity;
        $this->type = $type;
        $this->properties = $properties;
    }
    /**
     * {@inheritdoc}
     */
    public function identity()
    {
        return $this->identity;
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
        return $this->type === $type;
    }
}

?>