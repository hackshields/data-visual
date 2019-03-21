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
class TrackingSetting extends \Google\AdsApi\AdWords\v201806\cm\Setting
{
    /**
     * @var string $trackingUrl
     */
    protected $trackingUrl = null;
    /**
     * @param string $SettingType
     * @param string $trackingUrl
     */
    public function __construct($SettingType = null, $trackingUrl = null)
    {
        parent::__construct($SettingType);
        $this->trackingUrl = $trackingUrl;
    }
    /**
     * @return string
     */
    public function getTrackingUrl()
    {
        return $this->trackingUrl;
    }
    /**
     * @param string $trackingUrl
     * @return \Google\AdsApi\AdWords\v201806\cm\TrackingSetting
     */
    public function setTrackingUrl($trackingUrl)
    {
        $this->trackingUrl = $trackingUrl;
        return $this;
    }
}

?>