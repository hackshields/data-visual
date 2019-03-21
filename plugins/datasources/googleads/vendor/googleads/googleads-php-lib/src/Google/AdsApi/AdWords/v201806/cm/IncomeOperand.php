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
class IncomeOperand extends \Google\AdsApi\AdWords\v201806\cm\FunctionArgumentOperand
{
    /**
     * @var string $tier
     */
    protected $tier = null;
    /**
     * @param string $FunctionArgumentOperandType
     * @param string $tier
     */
    public function __construct($FunctionArgumentOperandType = null, $tier = null)
    {
        parent::__construct($FunctionArgumentOperandType);
        $this->tier = $tier;
    }
    /**
     * @return string
     */
    public function getTier()
    {
        return $this->tier;
    }
    /**
     * @param string $tier
     * @return \Google\AdsApi\AdWords\v201806\cm\IncomeOperand
     */
    public function setTier($tier)
    {
        $this->tier = $tier;
        return $this;
    }
}

?>