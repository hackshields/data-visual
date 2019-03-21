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
namespace GraphAware\Neo4j\Client\Result;

use GraphAware\Common\Result\RecordCursorInterface;
use GraphAware\Common\Result\ResultCollection as BaseResultCollection;
class ResultCollection extends BaseResultCollection
{
    /**
     * @var string|null
     */
    protected $tag;
    /**
     * @param string $tag
     */
    public function setTag($tag)
    {
        $this->tag = $tag;
    }
    /**
     * @return null|string
     */
    public function getTag()
    {
        return $this->tag;
    }
    /**
     * @param RecordCursorInterface $result
     *
     * @return ResultCollection
     */
    public static function withResult(RecordCursorInterface $result)
    {
        $coll = new self();
        $coll->add($result);
        return $coll;
    }
}

?>