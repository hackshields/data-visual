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

class Label
{
    /**
     * @var string
     */
    protected $name;
    /**
     * @param string $name
     */
    public function __construct($name)
    {
        $this->name = (string) $name;
    }
    /**
     * @param string $name
     *
     * @return Label
     */
    public static function label($name)
    {
        return new self($name);
    }
    /**
     * @return string
     */
    public function getName()
    {
        return $this->name;
    }
    /**
     * @return string
     */
    public function __toString()
    {
        return $this->name;
    }
}

?>