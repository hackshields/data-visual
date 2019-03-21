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
class ProductChannelExclusivity extends \Google\AdsApi\AdWords\v201809\cm\ProductDimension
{
    /**
     * @var string $channelExclusivity
     */
    protected $channelExclusivity = null;
    /**
     * @param string $ProductDimensionType
     * @param string $channelExclusivity
     */
    public function __construct($ProductDimensionType = null, $channelExclusivity = null)
    {
        parent::__construct($ProductDimensionType);
        $this->channelExclusivity = $channelExclusivity;
    }
    /**
     * @return string
     */
    public function getChannelExclusivity()
    {
        return $this->channelExclusivity;
    }
    /**
     * @param string $channelExclusivity
     * @return \Google\AdsApi\AdWords\v201809\cm\ProductChannelExclusivity
     */
    public function setChannelExclusivity($channelExclusivity)
    {
        $this->channelExclusivity = $channelExclusivity;
        return $this;
    }
}

?>