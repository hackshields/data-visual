<?php
/*
 * @ PHP 5.6
 * @ Decoder version : 1.0.0.1
 * @ Release on : 24.03.2018
 * @ Website    : http://EasyToYou.eu
 */

namespace Google\AdsApi\AdWords\v201806\o;

/**
 * This file was generated from WSDL. DO NOT EDIT.
 */
class MonthlySearchVolumeAttribute extends \Google\AdsApi\AdWords\v201806\o\Attribute
{
    /**
     * @var \Google\AdsApi\AdWords\v201806\o\MonthlySearchVolume[] $value
     */
    protected $value = null;
    /**
     * @param string $AttributeType
     * @param \Google\AdsApi\AdWords\v201806\o\MonthlySearchVolume[] $value
     */
    public function __construct($AttributeType = null, array $value = null)
    {
        parent::__construct($AttributeType);
        $this->value = $value;
    }
    /**
     * @return \Google\AdsApi\AdWords\v201806\o\MonthlySearchVolume[]
     */
    public function getValue()
    {
        return $this->value;
    }
    /**
     * @param \Google\AdsApi\AdWords\v201806\o\MonthlySearchVolume[] $value
     * @return \Google\AdsApi\AdWords\v201806\o\MonthlySearchVolumeAttribute
     */
    public function setValue(array $value)
    {
        $this->value = $value;
        return $this;
    }
}

?>