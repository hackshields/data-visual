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
namespace GraphAware\Bolt\Tests\Result;

use GraphAware\Bolt\Result\Type\MapAccess;
class DummyMA extends MapAccess
{
    public function __construct(array $properties)
    {
        $this->properties = $properties;
    }
}

?>