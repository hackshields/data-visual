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
namespace Google\AdsApi\AdWords\v201806\cm;

/**
 * This file was generated from WSDL. DO NOT EDIT.
 */
class PageFeed
{
    /**
     * @var int[] $feedIds
     */
    protected $feedIds = null;
    /**
     * @param int[] $feedIds
     */
    public function __construct(array $feedIds = null)
    {
        $this->feedIds = $feedIds;
    }
    /**
     * @return int[]
     */
    public function getFeedIds()
    {
        return $this->feedIds;
    }
    /**
     * @param int[] $feedIds
     * @return \Google\AdsApi\AdWords\v201806\cm\PageFeed
     */
    public function setFeedIds(array $feedIds)
    {
        $this->feedIds = $feedIds;
        return $this;
    }
}

?>