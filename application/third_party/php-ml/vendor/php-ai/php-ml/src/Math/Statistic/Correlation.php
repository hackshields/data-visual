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
namespace Phpml\Math\Statistic;

use Phpml\Exception\InvalidArgumentException;
class Correlation
{
    /**
     * @param array|int[]|float[] $x
     * @param array|int[]|float[] $y
     *
     * @throws InvalidArgumentException
     */
    public static function pearson(array $x, array $y) : float
    {
        if (count($x) !== count($y)) {
            throw InvalidArgumentException::arraySizeNotMatch();
        }
        $count = count($x);
        $meanX = Mean::arithmetic($x);
        $meanY = Mean::arithmetic($y);
        $axb = 0;
        $a2 = 0;
        $b2 = 0;
        for ($i = 0; $i < $count; ++$i) {
            $a = $x[$i] - $meanX;
            $b = $y[$i] - $meanY;
            $axb += $a * $b;
            $a2 += pow($a, 2);
            $b2 += pow($b, 2);
        }
        $corr = $axb / sqrt((double) ($a2 * $b2));
        return $corr;
    }
}

?>