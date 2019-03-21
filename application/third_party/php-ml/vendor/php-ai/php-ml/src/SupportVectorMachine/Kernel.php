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
namespace Phpml\SupportVectorMachine;

abstract class Kernel
{
    /**
     * u'*v.
     */
    public const LINEAR = 0;
    /**
     * (gamma*u'*v + coef0)^degree.
     */
    public const POLYNOMIAL = 1;
    /**
     * exp(-gamma*|u-v|^2).
     */
    public const RBF = 2;
    /**
     * tanh(gamma*u'*v + coef0).
     */
    public const SIGMOID = 3;
}

?>