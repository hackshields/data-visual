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
class AudienceSegmentPremiumFeature extends \Google\AdsApi\AdManager\v201811\PremiumFeature
{
    /**
     * @var int $audienceSegmentId
     */
    protected $audienceSegmentId = null;
    /**
     * @param int $audienceSegmentId
     */
    public function __construct($audienceSegmentId = null)
    {
        $this->audienceSegmentId = $audienceSegmentId;
    }
    /**
     * @return int
     */
    public function getAudienceSegmentId()
    {
        return $this->audienceSegmentId;
    }
    /**
     * @param int $audienceSegmentId
     * @return \Google\AdsApi\AdManager\v201811\AudienceSegmentPremiumFeature
     */
    public function setAudienceSegmentId($audienceSegmentId)
    {
        $this->audienceSegmentId = !is_null($audienceSegmentId) && PHP_INT_SIZE === 4 ? floatval($audienceSegmentId) : $audienceSegmentId;
        return $this;
    }
}

?>