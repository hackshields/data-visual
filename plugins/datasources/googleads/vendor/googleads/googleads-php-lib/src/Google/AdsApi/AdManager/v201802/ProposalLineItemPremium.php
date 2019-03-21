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
class ProposalLineItemPremium
{
    /**
     * @var \Google\AdsApi\AdManager\v201802\PremiumRateValue $premiumRateValue
     */
    protected $premiumRateValue = null;
    /**
     * @var string $status
     */
    protected $status = null;
    /**
     * @param \Google\AdsApi\AdManager\v201802\PremiumRateValue $premiumRateValue
     * @param string $status
     */
    public function __construct($premiumRateValue = null, $status = null)
    {
        $this->premiumRateValue = $premiumRateValue;
        $this->status = $status;
    }
    /**
     * @return \Google\AdsApi\AdManager\v201802\PremiumRateValue
     */
    public function getPremiumRateValue()
    {
        return $this->premiumRateValue;
    }
    /**
     * @param \Google\AdsApi\AdManager\v201802\PremiumRateValue $premiumRateValue
     * @return \Google\AdsApi\AdManager\v201802\ProposalLineItemPremium
     */
    public function setPremiumRateValue($premiumRateValue)
    {
        $this->premiumRateValue = $premiumRateValue;
        return $this;
    }
    /**
     * @return string
     */
    public function getStatus()
    {
        return $this->status;
    }
    /**
     * @param string $status
     * @return \Google\AdsApi\AdManager\v201802\ProposalLineItemPremium
     */
    public function setStatus($status)
    {
        $this->status = $status;
        return $this;
    }
}

?>