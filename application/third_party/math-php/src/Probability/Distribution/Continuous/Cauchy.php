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
namespace MathPHP\Probability\Distribution\Continuous;

use MathPHP\Functions\Support;
/**
 * Cauchy distribution
 * https://en.wikipedia.org/wiki/Cauchy_distribution
 */
class Cauchy extends Continuous
{
    /**
     * Distribution parameter bounds limits
     * x₀ ∈ (-∞,∞)
     * γ  ∈ (0,∞)
     * @var array
     */
    const PARAMETER_LIMITS = ['x₀' => '(-∞,∞)', 'γ' => '(0,∞)'];
    /**
     * Distribution support bounds limits
     * x  ∈ (-∞,∞)
     * @var array
     */
    const SUPPORT_LIMITS = ['x' => '(-∞,∞)'];
    /** @var number Location Parameter */
    protected $x₀;
    /** @var number Scale Parameter */
    protected $γ;
    /**
     * Constructor
     *
     * @param number $x₀ location parameter
     * @param number $γ  scale parameter γ > 0
     */
    public function __construct($x₀, $γ)
    {
        parent::__construct($x₀, $γ);
    }
    /**
     * Probability density function
     *
     *                1
     *    --------------------------
     *       ┌        / x - x₀ \ ² ┐
     *    πγ | 1  +  | ---------|  |
     *       └        \    γ   /   ┘
     *
     * @param float $x
     *
     * @return number
     */
    public function pdf(float $x)
    {
        Support::checkLimits(self::SUPPORT_LIMITS, ['x' => $x]);
        $x₀ = $this->x₀;
        $γ = $this->γ;
        $π = \M_PI;
        return 1 / ($π * $γ * (1 + (($x - $x₀) / $γ) ** 2));
    }
    /**
     * Cumulative distribution function
     * Calculate the cumulative value value up to a point, left tail.
     *
     * @param float $x
     *
     * @return number
     */
    public function cdf(float $x)
    {
        Support::checkLimits(self::SUPPORT_LIMITS, ['x' => $x]);
        $x₀ = $this->x₀;
        $γ = $this->γ;
        $π = \M_PI;
        return 1 / $π * atan(($x - $x₀) / $γ) + 0.5;
    }
    /**
     * Mean of the distribution (undefined)
     *
     * μ is undefined
     *
     * @return null
     */
    public function mean()
    {
        return \NAN;
    }
    /**
     * Median of the distribution
     *
     * @return number x₀
     */
    public function median()
    {
        return $this->x₀;
    }
    /**
     * Mode of the distribution
     *
     * @return number x₀
     */
    public function mode()
    {
        return $this->x₀;
    }
    /**
     * Inverse CDF (Quantile function)
     *
     * Q(p;x₀,γ) = x₀ + γ tan[π(p - ½)]
     *
     * @param float $p
     *
     * @return number
     */
    public function inverse(float $p)
    {
        Support::checkLimits(['p' => '[0,1]'], ['p' => $p]);
        $x₀ = $this->x₀;
        $γ = $this->γ;
        $π = \M_PI;
        return $x₀ + $γ * tan($π * ($p - 0.5));
    }
}

?>