<?php
/*
 * @ PHP 5.6
 * @ Decoder version : 1.0.0.1
 * @ Release on : 24.03.2018
 * @ Website    : http://EasyToYou.eu
 */

namespace Google\AdsApi\AdWords\v201809\cm;

/**
 * This file was generated from WSDL. DO NOT EDIT.
 */
class FeedItemCampaignTargeting
{
    /**
     * @var int $TargetingCampaignId
     */
    protected $TargetingCampaignId = null;
    /**
     * @param int $TargetingCampaignId
     */
    public function __construct($TargetingCampaignId = null)
    {
        $this->TargetingCampaignId = $TargetingCampaignId;
    }
    /**
     * @return int
     */
    public function getTargetingCampaignId()
    {
        return $this->TargetingCampaignId;
    }
    /**
     * @param int $TargetingCampaignId
     * @return \Google\AdsApi\AdWords\v201809\cm\FeedItemCampaignTargeting
     */
    public function setTargetingCampaignId($TargetingCampaignId)
    {
        $this->TargetingCampaignId = !is_null($TargetingCampaignId) && PHP_INT_SIZE === 4 ? floatval($TargetingCampaignId) : $TargetingCampaignId;
        return $this;
    }
}

?>