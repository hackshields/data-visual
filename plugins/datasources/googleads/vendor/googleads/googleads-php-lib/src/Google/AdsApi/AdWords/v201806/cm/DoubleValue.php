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
class DoubleValue extends \Google\AdsApi\AdWords\v201806\cm\NumberValue
{
    /**
     * @var float $number
     */
    protected $number = null;
    /**
     * @param string $ComparableValueType
     * @param float $number
     */
    public function __construct($ComparableValueType = null, $number = null)
    {
        parent::__construct($ComparableValueType);
        $this->number = $number;
    }
    /**
     * @return float
     */
    public function getNumber()
    {
        return $this->number;
    }
    /**
     * @param float $number
     * @return \Google\AdsApi\AdWords\v201806\cm\DoubleValue
     */
    public function setNumber($number)
    {
        $this->number = $number;
        return $this;
    }
}

?>