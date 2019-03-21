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
namespace GraphAware\Common\Driver;

interface PipelineInterface
{
    /**
     * @param string $query
     * @param array  $parameters
     * @param null   $tag
     */
    public function push($query, array $parameters = array(), $tag = null);
    /**
     * @return \GraphAware\Common\Result\ResultCollection
     */
    public function run();
}

?>