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
namespace Google\AdsApi\AdManager\v201805;

/**
 * This file was generated from WSDL. DO NOT EDIT.
 */
class MobileCarrierTargeting
{
    /**
     * @var boolean $isTargeted
     */
    protected $isTargeted = null;
    /**
     * @var \Google\AdsApi\AdManager\v201805\Technology[] $mobileCarriers
     */
    protected $mobileCarriers = null;
    /**
     * @param boolean $isTargeted
     * @param \Google\AdsApi\AdManager\v201805\Technology[] $mobileCarriers
     */
    public function __construct($isTargeted = null, array $mobileCarriers = null)
    {
        $this->isTargeted = $isTargeted;
        $this->mobileCarriers = $mobileCarriers;
    }
    /**
     * @return boolean
     */
    public function getIsTargeted()
    {
        return $this->isTargeted;
    }
    /**
     * @param boolean $isTargeted
     * @return \Google\AdsApi\AdManager\v201805\MobileCarrierTargeting
     */
    public function setIsTargeted($isTargeted)
    {
        $this->isTargeted = $isTargeted;
        return $this;
    }
    /**
     * @return \Google\AdsApi\AdManager\v201805\Technology[]
     */
    public function getMobileCarriers()
    {
        return $this->mobileCarriers;
    }
    /**
     * @param \Google\AdsApi\AdManager\v201805\Technology[] $mobileCarriers
     * @return \Google\AdsApi\AdManager\v201805\MobileCarrierTargeting
     */
    public function setMobileCarriers(array $mobileCarriers)
    {
        $this->mobileCarriers = $mobileCarriers;
        return $this;
    }
}

?>