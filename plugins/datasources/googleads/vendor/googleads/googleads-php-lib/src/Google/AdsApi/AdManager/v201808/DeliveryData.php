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
namespace Google\AdsApi\AdManager\v201808;

/**
 * This file was generated from WSDL. DO NOT EDIT.
 */
class DeliveryData
{
    /**
     * @var int[] $units
     */
    protected $units = null;
    /**
     * @param int[] $units
     */
    public function __construct(array $units = null)
    {
        $this->units = $units;
    }
    /**
     * @return int[]
     */
    public function getUnits()
    {
        return $this->units;
    }
    /**
     * @param int[] $units
     * @return \Google\AdsApi\AdManager\v201808\DeliveryData
     */
    public function setUnits(array $units)
    {
        $this->units = $units;
        return $this;
    }
}

?>