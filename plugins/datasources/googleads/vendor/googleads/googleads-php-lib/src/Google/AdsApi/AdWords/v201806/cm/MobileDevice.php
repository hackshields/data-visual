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
class MobileDevice extends \Google\AdsApi\AdWords\v201806\cm\Criterion
{
    /**
     * @var string $deviceName
     */
    protected $deviceName = null;
    /**
     * @var string $manufacturerName
     */
    protected $manufacturerName = null;
    /**
     * @var string $deviceType
     */
    protected $deviceType = null;
    /**
     * @var string $operatingSystemName
     */
    protected $operatingSystemName = null;
    /**
     * @param int $id
     * @param string $type
     * @param string $CriterionType
     * @param string $deviceName
     * @param string $manufacturerName
     * @param string $deviceType
     * @param string $operatingSystemName
     */
    public function __construct($id = null, $type = null, $CriterionType = null, $deviceName = null, $manufacturerName = null, $deviceType = null, $operatingSystemName = null)
    {
        parent::__construct($id, $type, $CriterionType);
        $this->deviceName = $deviceName;
        $this->manufacturerName = $manufacturerName;
        $this->deviceType = $deviceType;
        $this->operatingSystemName = $operatingSystemName;
    }
    /**
     * @return string
     */
    public function getDeviceName()
    {
        return $this->deviceName;
    }
    /**
     * @param string $deviceName
     * @return \Google\AdsApi\AdWords\v201806\cm\MobileDevice
     */
    public function setDeviceName($deviceName)
    {
        $this->deviceName = $deviceName;
        return $this;
    }
    /**
     * @return string
     */
    public function getManufacturerName()
    {
        return $this->manufacturerName;
    }
    /**
     * @param string $manufacturerName
     * @return \Google\AdsApi\AdWords\v201806\cm\MobileDevice
     */
    public function setManufacturerName($manufacturerName)
    {
        $this->manufacturerName = $manufacturerName;
        return $this;
    }
    /**
     * @return string
     */
    public function getDeviceType()
    {
        return $this->deviceType;
    }
    /**
     * @param string $deviceType
     * @return \Google\AdsApi\AdWords\v201806\cm\MobileDevice
     */
    public function setDeviceType($deviceType)
    {
        $this->deviceType = $deviceType;
        return $this;
    }
    /**
     * @return string
     */
    public function getOperatingSystemName()
    {
        return $this->operatingSystemName;
    }
    /**
     * @param string $operatingSystemName
     * @return \Google\AdsApi\AdWords\v201806\cm\MobileDevice
     */
    public function setOperatingSystemName($operatingSystemName)
    {
        $this->operatingSystemName = $operatingSystemName;
        return $this;
    }
}

?>