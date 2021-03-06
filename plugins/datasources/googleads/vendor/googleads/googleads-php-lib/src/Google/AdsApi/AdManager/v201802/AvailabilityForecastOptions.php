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
namespace Google\AdsApi\AdManager\v201802;

/**
 * This file was generated from WSDL. DO NOT EDIT.
 */
class AvailabilityForecastOptions
{
    /**
     * @var boolean $includeTargetingCriteriaBreakdown
     */
    protected $includeTargetingCriteriaBreakdown = null;
    /**
     * @var boolean $includeContendingLineItems
     */
    protected $includeContendingLineItems = null;
    /**
     * @param boolean $includeTargetingCriteriaBreakdown
     * @param boolean $includeContendingLineItems
     */
    public function __construct($includeTargetingCriteriaBreakdown = null, $includeContendingLineItems = null)
    {
        $this->includeTargetingCriteriaBreakdown = $includeTargetingCriteriaBreakdown;
        $this->includeContendingLineItems = $includeContendingLineItems;
    }
    /**
     * @return boolean
     */
    public function getIncludeTargetingCriteriaBreakdown()
    {
        return $this->includeTargetingCriteriaBreakdown;
    }
    /**
     * @param boolean $includeTargetingCriteriaBreakdown
     * @return \Google\AdsApi\AdManager\v201802\AvailabilityForecastOptions
     */
    public function setIncludeTargetingCriteriaBreakdown($includeTargetingCriteriaBreakdown)
    {
        $this->includeTargetingCriteriaBreakdown = $includeTargetingCriteriaBreakdown;
        return $this;
    }
    /**
     * @return boolean
     */
    public function getIncludeContendingLineItems()
    {
        return $this->includeContendingLineItems;
    }
    /**
     * @param boolean $includeContendingLineItems
     * @return \Google\AdsApi\AdManager\v201802\AvailabilityForecastOptions
     */
    public function setIncludeContendingLineItems($includeContendingLineItems)
    {
        $this->includeContendingLineItems = $includeContendingLineItems;
        return $this;
    }
}

?>