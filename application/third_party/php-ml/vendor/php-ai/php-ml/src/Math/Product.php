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
namespace Phpml\Math;

class Product
{
    /**
     * @return mixed
     */
    public static function scalar(array $a, array $b)
    {
        $product = 0;
        foreach ($a as $index => $value) {
            if (is_numeric($value) && is_numeric($b[$index])) {
                $product += $value * $b[$index];
            }
        }
        return $product;
    }
}

?>