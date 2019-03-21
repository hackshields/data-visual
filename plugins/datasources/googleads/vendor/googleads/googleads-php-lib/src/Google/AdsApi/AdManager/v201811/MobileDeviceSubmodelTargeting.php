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
class MobileDeviceSubmodelTargeting
{
    /**
     * @var \Google\AdsApi\AdManager\v201811\Technology[] $targetedMobileDeviceSubmodels
     */
    protected $targetedMobileDeviceSubmodels = null;
    /**
     * @var \Google\AdsApi\AdManager\v201811\Technology[] $excludedMobileDeviceSubmodels
     */
    protected $excludedMobileDeviceSubmodels = null;
    /**
     * @param \Google\AdsApi\AdManager\v201811\Technology[] $targetedMobileDeviceSubmodels
     * @param \Google\AdsApi\AdManager\v201811\Technology[] $excludedMobileDeviceSubmodels
     */
    public function __construct(array $targetedMobileDeviceSubmodels = null, array $excludedMobileDeviceSubmodels = null)
    {
        $this->targetedMobileDeviceSubmodels = $targetedMobileDeviceSubmodels;
        $this->excludedMobileDeviceSubmodels = $excludedMobileDeviceSubmodels;
    }
    /**
     * @return \Google\AdsApi\AdManager\v201811\Technology[]
     */
    public function getTargetedMobileDeviceSubmodels()
    {
        return $this->targetedMobileDeviceSubmodels;
    }
    /**
     * @param \Google\AdsApi\AdManager\v201811\Technology[] $targetedMobileDeviceSubmodels
     * @return \Google\AdsApi\AdManager\v201811\MobileDeviceSubmodelTargeting
     */
    public function setTargetedMobileDeviceSubmodels(array $targetedMobileDeviceSubmodels)
    {
        $this->targetedMobileDeviceSubmodels = $targetedMobileDeviceSubmodels;
        return $this;
    }
    /**
     * @return \Google\AdsApi\AdManager\v201811\Technology[]
     */
    public function getExcludedMobileDeviceSubmodels()
    {
        return $this->excludedMobileDeviceSubmodels;
    }
    /**
     * @param \Google\AdsApi\AdManager\v201811\Technology[] $excludedMobileDeviceSubmodels
     * @return \Google\AdsApi\AdManager\v201811\MobileDeviceSubmodelTargeting
     */
    public function setExcludedMobileDeviceSubmodels(array $excludedMobileDeviceSubmodels)
    {
        $this->excludedMobileDeviceSubmodels = $excludedMobileDeviceSubmodels;
        return $this;
    }
}

?>