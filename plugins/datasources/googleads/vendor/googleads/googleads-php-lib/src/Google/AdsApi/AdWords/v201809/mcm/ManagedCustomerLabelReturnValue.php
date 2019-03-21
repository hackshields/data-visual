<?php
/*
 * @ PHP 5.6
 * @ Decoder version : 1.0.0.1
 * @ Release on : 24.03.2018
 * @ Website    : http://EasyToYou.eu
 */

namespace Google\AdsApi\AdWords\v201809\mcm;

/**
 * This file was generated from WSDL. DO NOT EDIT.
 */
class ManagedCustomerLabelReturnValue
{
    /**
     * @var \Google\AdsApi\AdWords\v201809\mcm\ManagedCustomerLabel[] $value
     */
    protected $value = null;
    /**
     * @param \Google\AdsApi\AdWords\v201809\mcm\ManagedCustomerLabel[] $value
     */
    public function __construct(array $value = null)
    {
        $this->value = $value;
    }
    /**
     * @return \Google\AdsApi\AdWords\v201809\mcm\ManagedCustomerLabel[]
     */
    public function getValue()
    {
        return $this->value;
    }
    /**
     * @param \Google\AdsApi\AdWords\v201809\mcm\ManagedCustomerLabel[] $value
     * @return \Google\AdsApi\AdWords\v201809\mcm\ManagedCustomerLabelReturnValue
     */
    public function setValue(array $value)
    {
        $this->value = $value;
        return $this;
    }
}

?>