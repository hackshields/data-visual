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
class FeedItemDevicePreference
{
    /**
     * @var int $devicePreference
     */
    protected $devicePreference = null;
    /**
     * @param int $devicePreference
     */
    public function __construct($devicePreference = null)
    {
        $this->devicePreference = $devicePreference;
    }
    /**
     * @return int
     */
    public function getDevicePreference()
    {
        return $this->devicePreference;
    }
    /**
     * @param int $devicePreference
     * @return \Google\AdsApi\AdWords\v201806\cm\FeedItemDevicePreference
     */
    public function setDevicePreference($devicePreference)
    {
        $this->devicePreference = !is_null($devicePreference) && PHP_INT_SIZE === 4 ? floatval($devicePreference) : $devicePreference;
        return $this;
    }
}

?>