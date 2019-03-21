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
class AdGroupAdRotationMode
{
    /**
     * @var string $adRotationMode
     */
    protected $adRotationMode = null;
    /**
     * @param string $adRotationMode
     */
    public function __construct($adRotationMode = null)
    {
        $this->adRotationMode = $adRotationMode;
    }
    /**
     * @return string
     */
    public function getAdRotationMode()
    {
        return $this->adRotationMode;
    }
    /**
     * @param string $adRotationMode
     * @return \Google\AdsApi\AdWords\v201809\cm\AdGroupAdRotationMode
     */
    public function setAdRotationMode($adRotationMode)
    {
        $this->adRotationMode = $adRotationMode;
        return $this;
    }
}

?>