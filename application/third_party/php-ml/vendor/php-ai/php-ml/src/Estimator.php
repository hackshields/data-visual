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
namespace Phpml;

interface Estimator
{
    /**
     * @param array $samples
     * @param array $targets
     */
    public function train(array $samples, array $targets);
    /**
     * @param array $samples
     *
     * @return mixed
     */
    public function predict(array $samples);
}

?>