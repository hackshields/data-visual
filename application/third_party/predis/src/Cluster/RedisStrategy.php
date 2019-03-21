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
 * This file is part of the Predis package.
 *
 * (c) Daniele Alessandri <suppakilla@gmail.com>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */
namespace Predis\Cluster;

use Predis\Cluster\Hash\CRC16;
use Predis\Cluster\Hash\HashGeneratorInterface;
use Predis\NotSupportedException;
/**
 * Default class used by Predis to calculate hashes out of keys of
 * commands supported by redis-cluster.
 *
 * @author Daniele Alessandri <suppakilla@gmail.com>
 */
class RedisStrategy extends ClusterStrategy
{
    protected $hashGenerator;
    /**
     * @param HashGeneratorInterface $hashGenerator Hash generator instance.
     */
    public function __construct(HashGeneratorInterface $hashGenerator = null)
    {
        parent::__construct();
        $this->hashGenerator = $hashGenerator ?: new CRC16();
    }
    /**
     * {@inheritdoc}
     */
    public function getSlotByKey($key)
    {
        $key = $this->extractKeyTag($key);
        $slot = $this->hashGenerator->hash($key) & 0x3fff;
        return $slot;
    }
    /**
     * {@inheritdoc}
     */
    public function getDistributor()
    {
        throw new NotSupportedException('This cluster strategy does not provide an external distributor');
    }
}

?>