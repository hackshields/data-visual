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
class NegativeAdGroupCriterion extends \Google\AdsApi\AdWords\v201806\cm\AdGroupCriterion
{
    /**
     * @param int $adGroupId
     * @param string $criterionUse
     * @param \Google\AdsApi\AdWords\v201806\cm\Criterion $criterion
     * @param \Google\AdsApi\AdWords\v201806\cm\Label[] $labels
     * @param \Google\AdsApi\AdWords\v201806\cm\String_StringMapEntry[] $forwardCompatibilityMap
     * @param int $baseCampaignId
     * @param int $baseAdGroupId
     * @param string $AdGroupCriterionType
     */
    public function __construct($adGroupId = null, $criterionUse = null, $criterion = null, array $labels = null, array $forwardCompatibilityMap = null, $baseCampaignId = null, $baseAdGroupId = null, $AdGroupCriterionType = null)
    {
        parent::__construct($adGroupId, $criterionUse, $criterion, $labels, $forwardCompatibilityMap, $baseCampaignId, $baseAdGroupId, $AdGroupCriterionType);
    }
}

?>