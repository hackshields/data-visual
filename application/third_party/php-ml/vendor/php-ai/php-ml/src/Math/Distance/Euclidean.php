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
namespace Phpml\Math\Distance;

use Phpml\Exception\InvalidArgumentException;
use Phpml\Math\Distance;
class Euclidean implements Distance
{
    /**
     * @throws InvalidArgumentException
     */
    public function distance(array $a, array $b) : float
    {
        if (count($a) !== count($b)) {
            throw InvalidArgumentException::arraySizeNotMatch();
        }
        $distance = 0;
        foreach ($a as $i => $val) {
            $distance += ($val - $b[$i]) ** 2;
        }
        return sqrt((double) $distance);
    }
    /**
     * Square of Euclidean distance
     */
    public function sqDistance(array $a, array $b) : float
    {
        return $this->distance($a, $b) ** 2;
    }
}

?>