<?php
/*
 * @ PHP 5.6
 * @ Decoder version : 1.0.0.1
 * @ Release on : 24.03.2018
 * @ Website    : http://EasyToYou.eu
 */

namespace Google\AdsApi\AdWords\v201809\cm;

/**
 * This file was generated from WSDL. DO NOT EDIT.
 */
class ExplorerAutoOptimizerSetting extends \Google\AdsApi\AdWords\v201809\cm\Setting
{
    /**
     * @var boolean $optIn
     */
    protected $optIn = null;
    /**
     * @param string $SettingType
     * @param boolean $optIn
     */
    public function __construct($SettingType = null, $optIn = null)
    {
        parent::__construct($SettingType);
        $this->optIn = $optIn;
    }
    /**
     * @return boolean
     */
    public function getOptIn()
    {
        return $this->optIn;
    }
    /**
     * @param boolean $optIn
     * @return \Google\AdsApi\AdWords\v201809\cm\ExplorerAutoOptimizerSetting
     */
    public function setOptIn($optIn)
    {
        $this->optIn = $optIn;
        return $this;
    }
}

?>