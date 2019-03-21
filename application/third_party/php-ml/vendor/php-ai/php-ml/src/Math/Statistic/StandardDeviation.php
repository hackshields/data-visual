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
class StandardDeviation
{
    /**
     * @param array|float[]|int[] $numbers
     */
    public static function population(array $numbers, bool $sample = true) : float
    {
        if (empty($numbers)) {
            throw InvalidArgumentException::arrayCantBeEmpty();
        }
        $n = count($numbers);
        if ($sample && $n === 1) {
            throw InvalidArgumentException::arraySizeTooSmall(2);
        }
        $mean = Mean::arithmetic($numbers);
        $carry = 0.0;
        foreach ($numbers as $val) {
            $carry += ($val - $mean) ** 2;
        }
        if ($sample) {
            --$n;
        }
        return sqrt((double) ($carry / $n));
    }
    /**
     * Sum of squares deviations
     * ∑⟮xᵢ - μ⟯²
     *
     * @param array|float[]|int[] $numbers
     */
    public static function sumOfSquares(array $numbers) : float
    {
        if (empty($numbers)) {
            throw InvalidArgumentException::arrayCantBeEmpty();
        }
        $mean = Mean::arithmetic($numbers);
        return array_sum(array_map(function ($val) use($mean) {
            return ($val - $mean) ** 2;
        }, $numbers));
    }
}

?>