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
namespace Phpml\Helper;

trait Trainable
{
    /**
     * @var array
     */
    private $samples = [];
    /**
     * @var array
     */
    private $targets = [];
    /**
     * @param array $samples
     * @param array $targets
     */
    public function train(array $samples, array $targets) : void
    {
        $this->samples = array_merge($this->samples, $samples);
        $this->targets = array_merge($this->targets, $targets);
    }
}

?>