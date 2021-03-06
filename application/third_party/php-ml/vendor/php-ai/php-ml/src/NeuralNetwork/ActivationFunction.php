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
namespace Phpml\NeuralNetwork;

interface ActivationFunction
{
    /**
     * @param float|int $value
     */
    public function compute($value) : float;
    /**
     * @param float|int $value
     * @param float|int $computedvalue
     */
    public function differentiate($value, $computedvalue) : float;
}

?>