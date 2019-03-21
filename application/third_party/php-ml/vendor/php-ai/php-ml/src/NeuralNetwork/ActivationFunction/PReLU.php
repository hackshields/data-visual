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
namespace Phpml\NeuralNetwork\ActivationFunction;

use Phpml\NeuralNetwork\ActivationFunction;
class PReLU implements ActivationFunction
{
    /**
     * @var float
     */
    private $beta;
    public function __construct(float $beta = 0.01)
    {
        $this->beta = $beta;
    }
    /**
     * @param float|int $value
     */
    public function compute($value) : float
    {
        return $value >= 0 ? $value : $this->beta * $value;
    }
    /**
     * @param float|int $value
     * @param float|int $computedvalue
     */
    public function differentiate($value, $computedvalue) : float
    {
        return $computedvalue >= 0 ? 1.0 : $this->beta;
    }
}

?>