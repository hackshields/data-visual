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
class FirstPartyAudienceSegmentRule
{
    /**
     * @var \Google\AdsApi\AdManager\v201802\InventoryTargeting $inventoryRule
     */
    protected $inventoryRule = null;
    /**
     * @var \Google\AdsApi\AdManager\v201802\CustomCriteriaSet $customCriteriaRule
     */
    protected $customCriteriaRule = null;
    /**
     * @param \Google\AdsApi\AdManager\v201802\InventoryTargeting $inventoryRule
     * @param \Google\AdsApi\AdManager\v201802\CustomCriteriaSet $customCriteriaRule
     */
    public function __construct($inventoryRule = null, $customCriteriaRule = null)
    {
        $this->inventoryRule = $inventoryRule;
        $this->customCriteriaRule = $customCriteriaRule;
    }
    /**
     * @return \Google\AdsApi\AdManager\v201802\InventoryTargeting
     */
    public function getInventoryRule()
    {
        return $this->inventoryRule;
    }
    /**
     * @param \Google\AdsApi\AdManager\v201802\InventoryTargeting $inventoryRule
     * @return \Google\AdsApi\AdManager\v201802\FirstPartyAudienceSegmentRule
     */
    public function setInventoryRule($inventoryRule)
    {
        $this->inventoryRule = $inventoryRule;
        return $this;
    }
    /**
     * @return \Google\AdsApi\AdManager\v201802\CustomCriteriaSet
     */
    public function getCustomCriteriaRule()
    {
        return $this->customCriteriaRule;
    }
    /**
     * @param \Google\AdsApi\AdManager\v201802\CustomCriteriaSet $customCriteriaRule
     * @return \Google\AdsApi\AdManager\v201802\FirstPartyAudienceSegmentRule
     */
    public function setCustomCriteriaRule($customCriteriaRule)
    {
        $this->customCriteriaRule = $customCriteriaRule;
        return $this;
    }
}

?>