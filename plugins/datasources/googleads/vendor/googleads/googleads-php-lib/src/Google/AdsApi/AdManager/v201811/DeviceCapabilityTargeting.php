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
class DeviceCapabilityTargeting
{
    /**
     * @var \Google\AdsApi\AdManager\v201811\Technology[] $targetedDeviceCapabilities
     */
    protected $targetedDeviceCapabilities = null;
    /**
     * @var \Google\AdsApi\AdManager\v201811\Technology[] $excludedDeviceCapabilities
     */
    protected $excludedDeviceCapabilities = null;
    /**
     * @param \Google\AdsApi\AdManager\v201811\Technology[] $targetedDeviceCapabilities
     * @param \Google\AdsApi\AdManager\v201811\Technology[] $excludedDeviceCapabilities
     */
    public function __construct(array $targetedDeviceCapabilities = null, array $excludedDeviceCapabilities = null)
    {
        $this->targetedDeviceCapabilities = $targetedDeviceCapabilities;
        $this->excludedDeviceCapabilities = $excludedDeviceCapabilities;
    }
    /**
     * @return \Google\AdsApi\AdManager\v201811\Technology[]
     */
    public function getTargetedDeviceCapabilities()
    {
        return $this->targetedDeviceCapabilities;
    }
    /**
     * @param \Google\AdsApi\AdManager\v201811\Technology[] $targetedDeviceCapabilities
     * @return \Google\AdsApi\AdManager\v201811\DeviceCapabilityTargeting
     */
    public function setTargetedDeviceCapabilities(array $targetedDeviceCapabilities)
    {
        $this->targetedDeviceCapabilities = $targetedDeviceCapabilities;
        return $this;
    }
    /**
     * @return \Google\AdsApi\AdManager\v201811\Technology[]
     */
    public function getExcludedDeviceCapabilities()
    {
        return $this->excludedDeviceCapabilities;
    }
    /**
     * @param \Google\AdsApi\AdManager\v201811\Technology[] $excludedDeviceCapabilities
     * @return \Google\AdsApi\AdManager\v201811\DeviceCapabilityTargeting
     */
    public function setExcludedDeviceCapabilities(array $excludedDeviceCapabilities)
    {
        $this->excludedDeviceCapabilities = $excludedDeviceCapabilities;
        return $this;
    }
}

?>