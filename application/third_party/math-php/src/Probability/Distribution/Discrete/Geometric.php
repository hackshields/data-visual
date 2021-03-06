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
namespace MathPHP\Probability\Distribution\Discrete;

use MathPHP\Functions\Support;
/**
 * Geometric distribution
 *
 * https://en.wikipedia.org/wiki/Geometric_distribution
 */
class Geometric extends Discrete
{
    /**
     * Distribution parameter bounds limits
     * p ∈ (0,1]
     * @var array
     */
    const PARAMETER_LIMITS = ['p' => '(0,1]'];
    /**
     * Distribution parameter bounds limits
     * k ∈ [1,∞)
     * @var array
     */
    const SUPPORT_LIMITS = ['k' => '[1,∞)'];
    /** @var float success probability  0 < p ≤ 1 */
    protected $p;
    /**
     * Constructor
     *
     * @param float $p success probability  0 < p ≤ 1
     */
    public function __construct(float $p)
    {
        parent::__construct($p);
    }
    /**
     * Probability mass function
     *
     * The probability distribution of the number Y = X − 1 of failures
     * before the first success, supported on the set { 0, 1, 2, 3, ... }
     *
     * k failures where k ∈ {0, 1, 2, 3, ...}
     *
     * pmf = (1 - p)ᵏp
     *
     * @param  int   $k number of trials     k ≥ 1
     *
     * @return float
     */
    public function pmf(int $k) : float
    {
        Support::checkLimits(self::SUPPORT_LIMITS, ['k' => $k]);
        $p = $this->p;
        $⟮1 − p⟯ᵏ = pow(1 - $p, $k);
        return $⟮1 − p⟯ᵏ * $p;
    }
    /**
     * Cumulative distribution function (lower cumulative)
     *
     * The probability distribution of the number Y = X − 1 of failures
     * before the first success, supported on the set { 0, 1, 2, 3, ... }
     *
     * k failures where k ∈ {0, 1, 2, 3, ...}
     *
     * pmf = 1 - (1 - p)ᵏ⁺¹
     *
     * @param  int   $k number of trials     k ≥ 0
     *
     * @return float
     */
    public function cdf(int $k) : float
    {
        Support::checkLimits(self::SUPPORT_LIMITS, ['k' => $k]);
        $p = $this->p;
        $⟮1 − p⟯ᵏ⁺¹ = pow(1 - $p, $k + 1);
        return 1 - $⟮1 − p⟯ᵏ⁺¹;
    }
}

?>