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
namespace Phpml\Dataset;

use Phpml\Exception\InvalidArgumentException;
class ArrayDataset implements Dataset
{
    /**
     * @var array
     */
    protected $samples = [];
    /**
     * @var array
     */
    protected $targets = [];
    /**
     * @throws InvalidArgumentException
     */
    public function __construct(array $samples, array $targets)
    {
        if (count($samples) != count($targets)) {
            throw InvalidArgumentException::arraySizeNotMatch();
        }
        $this->samples = $samples;
        $this->targets = $targets;
    }
    public function getSamples() : array
    {
        return $this->samples;
    }
    public function getTargets() : array
    {
        return $this->targets;
    }
}

?>