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
namespace Phpml\Metric;

use Phpml\Exception\InvalidArgumentException;
class Accuracy
{
    /**
     * @return float|int
     *
     * @throws InvalidArgumentException
     */
    public static function score(array $actualLabels, array $predictedLabels, bool $normalize = true)
    {
        if (count($actualLabels) != count($predictedLabels)) {
            throw InvalidArgumentException::arraySizeNotMatch();
        }
        $score = 0;
        foreach ($actualLabels as $index => $label) {
            if ($label == $predictedLabels[$index]) {
                ++$score;
            }
        }
        if ($normalize) {
            $score /= count($actualLabels);
        }
        return $score;
    }
}

?>