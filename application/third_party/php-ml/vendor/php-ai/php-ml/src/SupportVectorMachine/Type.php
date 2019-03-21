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

abstract class Type
{
    /**
     * classification.
     */
    public const C_SVC = 0;
    /**
     * classification.
     */
    public const NU_SVC = 1;
    /**
     * distribution estimation.
     */
    public const ONE_CLASS_SVM = 2;
    /**
     * regression.
     */
    public const EPSILON_SVR = 3;
    /**
     * regression.
     */
    public const NU_SVR = 4;
}

?>