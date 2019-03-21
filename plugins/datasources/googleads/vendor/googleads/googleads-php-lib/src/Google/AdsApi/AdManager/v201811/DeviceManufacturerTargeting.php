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
class DeviceManufacturerTargeting
{
    /**
     * @var boolean $isTargeted
     */
    protected $isTargeted = null;
    /**
     * @var \Google\AdsApi\AdManager\v201811\Technology[] $deviceManufacturers
     */
    protected $deviceManufacturers = null;
    /**
     * @param boolean $isTargeted
     * @param \Google\AdsApi\AdManager\v201811\Technology[] $deviceManufacturers
     */
    public function __construct($isTargeted = null, array $deviceManufacturers = null)
    {
        $this->isTargeted = $isTargeted;
        $this->deviceManufacturers = $deviceManufacturers;
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
     * @return \Google\AdsApi\AdManager\v201811\DeviceManufacturerTargeting
     */
    public function setIsTargeted($isTargeted)
    {
        $this->isTargeted = $isTargeted;
        return $this;
    }
    /**
     * @return \Google\AdsApi\AdManager\v201811\Technology[]
     */
    public function getDeviceManufacturers()
    {
        return $this->deviceManufacturers;
    }
    /**
     * @param \Google\AdsApi\AdManager\v201811\Technology[] $deviceManufacturers
     * @return \Google\AdsApi\AdManager\v201811\DeviceManufacturerTargeting
     */
    public function setDeviceManufacturers(array $deviceManufacturers)
    {
        $this->deviceManufacturers = $deviceManufacturers;
        return $this;
    }
}

?>