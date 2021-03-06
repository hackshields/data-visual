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
class RequestContextOperand extends \Google\AdsApi\AdWords\v201806\cm\FunctionArgumentOperand
{
    /**
     * @var string $contextType
     */
    protected $contextType = null;
    /**
     * @param string $FunctionArgumentOperandType
     * @param string $contextType
     */
    public function __construct($FunctionArgumentOperandType = null, $contextType = null)
    {
        parent::__construct($FunctionArgumentOperandType);
        $this->contextType = $contextType;
    }
    /**
     * @return string
     */
    public function getContextType()
    {
        return $this->contextType;
    }
    /**
     * @param string $contextType
     * @return \Google\AdsApi\AdWords\v201806\cm\RequestContextOperand
     */
    public function setContextType($contextType)
    {
        $this->contextType = $contextType;
        return $this;
    }
}

?>