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
namespace MathPHP\Probability\Distribution;

use MathPHP\Functions\Support;
abstract class Distribution
{
    /**
     * Constructor
     *
     * @param number[] $params
     */
    public function __construct(...$params)
    {
        $new_params = static::PARAMETER_LIMITS;
        $i = 0;
        foreach ($new_params as $key => $value) {
            $this->{$key} = $params[$i];
            $new_params[$key] = $params[$i];
            $i++;
        }
        Support::checkLimits(static::PARAMETER_LIMITS, $new_params);
    }
}

?>