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
class Money extends \Google\AdsApi\AdWords\v201806\cm\ComparableValue
{
    /**
     * @var int $microAmount
     */
    protected $microAmount = null;
    /**
     * @param string $ComparableValueType
     * @param int $microAmount
     */
    public function __construct($ComparableValueType = null, $microAmount = null)
    {
        parent::__construct($ComparableValueType);
        $this->microAmount = $microAmount;
    }
    /**
     * @return int
     */
    public function getMicroAmount()
    {
        return $this->microAmount;
    }
    /**
     * @param int $microAmount
     * @return \Google\AdsApi\AdWords\v201806\cm\Money
     */
    public function setMicroAmount($microAmount)
    {
        $this->microAmount = !is_null($microAmount) && PHP_INT_SIZE === 4 ? floatval($microAmount) : $microAmount;
        return $this;
    }
}

?>