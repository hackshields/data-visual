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

interface IncrementalEstimator
{
    /**
     * @param array $samples
     * @param array $targets
     * @param array $labels
     */
    public function partialTrain(array $samples, array $targets, array $labels = []);
}

?>