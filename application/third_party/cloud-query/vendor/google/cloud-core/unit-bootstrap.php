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
use Google\ApiCore\Testing\MessageAwareArrayComparator;
use Google\ApiCore\Testing\ProtobufMessageComparator;
use Google\ApiCore\Testing\ProtobufGPBEmptyComparator;
date_default_timezone_set('UTC');
\SebastianBergmann\Comparator\Factory::getInstance()->register(new MessageAwareArrayComparator());
\SebastianBergmann\Comparator\Factory::getInstance()->register(new ProtobufMessageComparator());
\SebastianBergmann\Comparator\Factory::getInstance()->register(new ProtobufGPBEmptyComparator());

?>