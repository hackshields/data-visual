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
namespace GraphAware\Neo4j\Client\Event;

use GraphAware\Common\Cypher\StatementInterface;
use Symfony\Component\EventDispatcher\Event;
class PreRunEvent extends Event
{
    /**
     * @var StatementInterface[]
     */
    private $statements;
    /**
     * @param StatementInterface[] $statements
     */
    public function __construct(array $statements)
    {
        $this->statements = $statements;
    }
    /**
     * @return StatementInterface[]
     */
    public function getStatements()
    {
        return $this->statements;
    }
}

?>