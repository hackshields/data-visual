<?php
/*
 * @ PHP 5.6
 * @ Decoder version : 1.0.0.1
 * @ Release on : 24.03.2018
 * @ Website    : http://EasyToYou.eu
 */

namespace Google\AdsApi\AdWords\v201806\mcm;

/**
 * This file was generated from WSDL. DO NOT EDIT.
 */
class ManagedCustomerReturnValue
{
    /**
     * @var \Google\AdsApi\AdWords\v201806\mcm\ManagedCustomer[] $value
     */
    protected $value = null;
    /**
     * @param \Google\AdsApi\AdWords\v201806\mcm\ManagedCustomer[] $value
     */
    public function __construct(array $value = null)
    {
        $this->value = $value;
    }
    /**
     * @return \Google\AdsApi\AdWords\v201806\mcm\ManagedCustomer[]
     */
    public function getValue()
    {
        return $this->value;
    }
    /**
     * @param \Google\AdsApi\AdWords\v201806\mcm\ManagedCustomer[] $value
     * @return \Google\AdsApi\AdWords\v201806\mcm\ManagedCustomerReturnValue
     */
    public function setValue(array $value)
    {
        $this->value = $value;
        return $this;
    }
}

?>