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
namespace Google\AdsApi\AdManager\v201811;

/**
 * This file was generated from WSDL. DO NOT EDIT.
 */
class VideoPositionWithinPod
{
    /**
     * @var int $index
     */
    protected $index = null;
    /**
     * @param int $index
     */
    public function __construct($index = null)
    {
        $this->index = $index;
    }
    /**
     * @return int
     */
    public function getIndex()
    {
        return $this->index;
    }
    /**
     * @param int $index
     * @return \Google\AdsApi\AdManager\v201811\VideoPositionWithinPod
     */
    public function setIndex($index)
    {
        $this->index = $index;
        return $this;
    }
}

?>