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

/**
 * Interface ContinuousDistribution
 */
interface ContinuousDistribution
{
    /**
     * Probability density function
     *
     * @param float $x
     *
     * @return mixed
     */
    public function pdf(float $x);
    /**
     * Cumulative distribution function
     *
     * @param float $x
     *
     * @return mixed
     */
    public function cdf(float $x);
    /**
     * Mean average
     *
     * @return mixed
     */
    public function mean();
}

?>