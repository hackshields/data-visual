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
class LongValue extends \Google\AdsApi\AdWords\v201806\cm\NumberValue
{
    /**
     * @var int $number
     */
    protected $number = null;
    /**
     * @param string $ComparableValueType
     * @param int $number
     */
    public function __construct($ComparableValueType = null, $number = null)
    {
        parent::__construct($ComparableValueType);
        $this->number = $number;
    }
    /**
     * @return int
     */
    public function getNumber()
    {
        return $this->number;
    }
    /**
     * @param int $number
     * @return \Google\AdsApi\AdWords\v201806\cm\LongValue
     */
    public function setNumber($number)
    {
        $this->number = !is_null($number) && PHP_INT_SIZE === 4 ? floatval($number) : $number;
        return $this;
    }
}

?>