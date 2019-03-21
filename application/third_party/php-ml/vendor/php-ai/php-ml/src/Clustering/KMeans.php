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
declare (strict_types=1);
namespace Phpml\Clustering;

use Phpml\Clustering\KMeans\Space;
use Phpml\Exception\InvalidArgumentException;
class KMeans implements Clusterer
{
    public const INIT_RANDOM = 1;
    public const INIT_KMEANS_PLUS_PLUS = 2;
    /**
     * @var int
     */
    private $clustersNumber;
    /**
     * @var int
     */
    private $initialization;
    public function __construct(int $clustersNumber, int $initialization = self::INIT_KMEANS_PLUS_PLUS)
    {
        if ($clustersNumber <= 0) {
            throw InvalidArgumentException::invalidClustersNumber();
        }
        $this->clustersNumber = $clustersNumber;
        $this->initialization = $initialization;
    }
    public function cluster(array $samples) : array
    {
        $space = new Space(count($samples[0]));
        foreach ($samples as $sample) {
            $space->addPoint($sample);
        }
        $clusters = [];
        foreach ($space->cluster($this->clustersNumber, $this->initialization) as $cluster) {
            $clusters[] = $cluster->getPoints();
        }
        return $clusters;
    }
}

?>