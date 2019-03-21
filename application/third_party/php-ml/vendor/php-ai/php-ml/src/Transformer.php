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

interface Transformer
{
    /**
     * most transformers don't require targets to train so null allow to use fit method without setting targets
     */
    public function fit(array $samples, ?array $targets = null) : void;
    public function transform(array &$samples) : void;
}

?>