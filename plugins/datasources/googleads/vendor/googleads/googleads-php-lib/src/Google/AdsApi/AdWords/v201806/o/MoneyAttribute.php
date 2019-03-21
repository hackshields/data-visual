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
class MoneyAttribute extends \Google\AdsApi\AdWords\v201806\o\Attribute
{
    /**
     * @var \Google\AdsApi\AdWords\v201806\cm\Money $value
     */
    protected $value = null;
    /**
     * @param string $AttributeType
     * @param \Google\AdsApi\AdWords\v201806\cm\Money $value
     */
    public function __construct($AttributeType = null, $value = null)
    {
        parent::__construct($AttributeType);
        $this->value = $value;
    }
    /**
     * @return \Google\AdsApi\AdWords\v201806\cm\Money
     */
    public function getValue()
    {
        return $this->value;
    }
    /**
     * @param \Google\AdsApi\AdWords\v201806\cm\Money $value
     * @return \Google\AdsApi\AdWords\v201806\o\MoneyAttribute
     */
    public function setValue($value)
    {
        $this->value = $value;
        return $this;
    }
}

?>