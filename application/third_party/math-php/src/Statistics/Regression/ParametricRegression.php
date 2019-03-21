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
namespace MathPHP\Statistics\Regression;

class ParametricRegression extends Regression
{
    /**
     * An array of model parameters
     */
    protected $parameters;
    /**
     * Have the parent separate the points into xs and ys.
     * Calculate the regression parameters
     */
    public function __construct(array $points)
    {
        parent::__construct($points);
        $this->calculate();
    }
    /**
     * Return the model as a string
     */
    public function __toString() : string
    {
        return $this->getEquation();
    }
    /**
     * Get the equation
     * Uses the model's getModelEquation method.
     *
     * @return string
     */
    public function getEquation()
    {
        return $this->getModelEquation($this->parameters);
    }
    /**
     * Get the parameters
     * Uses the model's getModelParameters method.
     *
     * @return array
     */
    public function getParameters()
    {
        return $this->getModelParameters($this->parameters);
    }
}

?>