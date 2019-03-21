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
namespace Phpml\CrossValidation;

use Phpml\Dataset\Dataset;
class RandomSplit extends Split
{
    protected function splitDataset(Dataset $dataset, float $testSize) : void
    {
        $samples = $dataset->getSamples();
        $labels = $dataset->getTargets();
        $datasetSize = count($samples);
        $testCount = count($this->testSamples);
        for ($i = $datasetSize; $i > 0; --$i) {
            $key = mt_rand(0, $datasetSize - 1);
            $setName = (count($this->testSamples) - $testCount) / $datasetSize >= $testSize ? 'train' : 'test';
            $this->{$setName . 'Samples'}[] = $samples[$key];
            $this->{$setName . 'Labels'}[] = $labels[$key];
        }
    }
}

?>