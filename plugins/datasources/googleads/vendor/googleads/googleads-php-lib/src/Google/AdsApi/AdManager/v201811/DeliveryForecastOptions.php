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
class DeliveryForecastOptions
{
    /**
     * @var int[] $ignoredLineItemIds
     */
    protected $ignoredLineItemIds = null;
    /**
     * @param int[] $ignoredLineItemIds
     */
    public function __construct(array $ignoredLineItemIds = null)
    {
        $this->ignoredLineItemIds = $ignoredLineItemIds;
    }
    /**
     * @return int[]
     */
    public function getIgnoredLineItemIds()
    {
        return $this->ignoredLineItemIds;
    }
    /**
     * @param int[] $ignoredLineItemIds
     * @return \Google\AdsApi\AdManager\v201811\DeliveryForecastOptions
     */
    public function setIgnoredLineItemIds(array $ignoredLineItemIds)
    {
        $this->ignoredLineItemIds = $ignoredLineItemIds;
        return $this;
    }
}

?>