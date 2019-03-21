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
namespace Phpml\Dataset\Demo;

use Phpml\Dataset\CsvDataset;
/**
 * Classes: 6
 * Samples per class:
 *      70 float processed building windows
 *      17 float processed vehicle windows
 *      76 non-float processed building windows
 *      13 containers
 *      9 tableware
 *      29 headlamps
 * Samples total: 214
 * Features per sample: 9.
 */
class GlassDataset extends CsvDataset
{
    public function __construct()
    {
        $filepath = __DIR__ . '/../../../data/glass.csv';
        parent::__construct($filepath, 9, true);
    }
}

?>