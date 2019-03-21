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
namespace GraphAware\Neo4j\Client\Tests\Integration;

use GraphAware\Neo4j\Client\Event\FailureEvent;
use GraphAware\Neo4j\Client\Event\PostRunEvent;
use GraphAware\Neo4j\Client\Event\PreRunEvent;
class EventListener
{
    public $hookedPreRun = false;
    public $hookedPostRun = false;
    public $e;
    public function onPreRun(PreRunEvent $event)
    {
        if (count($event->getStatements()) > 0) {
            $this->hookedPreRun = true;
        }
    }
    public function onPostRun(PostRunEvent $event)
    {
        if ($event->getResults()->size() > 0) {
            $this->hookedPostRun = true;
        }
    }
    public function onFailure(FailureEvent $event)
    {
        $this->e = $event->getException();
        $event->disableException();
    }
}

?>