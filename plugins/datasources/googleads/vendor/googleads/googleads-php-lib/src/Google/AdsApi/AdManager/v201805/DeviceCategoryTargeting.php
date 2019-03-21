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
class DeviceCategoryTargeting
{
    /**
     * @var \Google\AdsApi\AdManager\v201805\Technology[] $targetedDeviceCategories
     */
    protected $targetedDeviceCategories = null;
    /**
     * @var \Google\AdsApi\AdManager\v201805\Technology[] $excludedDeviceCategories
     */
    protected $excludedDeviceCategories = null;
    /**
     * @param \Google\AdsApi\AdManager\v201805\Technology[] $targetedDeviceCategories
     * @param \Google\AdsApi\AdManager\v201805\Technology[] $excludedDeviceCategories
     */
    public function __construct(array $targetedDeviceCategories = null, array $excludedDeviceCategories = null)
    {
        $this->targetedDeviceCategories = $targetedDeviceCategories;
        $this->excludedDeviceCategories = $excludedDeviceCategories;
    }
    /**
     * @return \Google\AdsApi\AdManager\v201805\Technology[]
     */
    public function getTargetedDeviceCategories()
    {
        return $this->targetedDeviceCategories;
    }
    /**
     * @param \Google\AdsApi\AdManager\v201805\Technology[] $targetedDeviceCategories
     * @return \Google\AdsApi\AdManager\v201805\DeviceCategoryTargeting
     */
    public function setTargetedDeviceCategories(array $targetedDeviceCategories)
    {
        $this->targetedDeviceCategories = $targetedDeviceCategories;
        return $this;
    }
    /**
     * @return \Google\AdsApi\AdManager\v201805\Technology[]
     */
    public function getExcludedDeviceCategories()
    {
        return $this->excludedDeviceCategories;
    }
    /**
     * @param \Google\AdsApi\AdManager\v201805\Technology[] $excludedDeviceCategories
     * @return \Google\AdsApi\AdManager\v201805\DeviceCategoryTargeting
     */
    public function setExcludedDeviceCategories(array $excludedDeviceCategories)
    {
        $this->excludedDeviceCategories = $excludedDeviceCategories;
        return $this;
    }
}

?>