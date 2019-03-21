<?php
/*
 * @ PHP 5.6
 * @ Decoder version : 1.0.0.1
 * @ Release on : 24.03.2018
 * @ Website    : http://EasyToYou.eu
 */

namespace Google\AdsApi\AdWords\v201809\cm;

/**
 * This file was generated from WSDL. DO NOT EDIT.
 */
class CustomerFeedReturnValue extends \Google\AdsApi\AdWords\v201809\cm\ListReturnValue
{
    /**
     * @var \Google\AdsApi\AdWords\v201809\cm\CustomerFeed[] $value
     */
    protected $value = null;
    /**
     * @param string $ListReturnValueType
     * @param \Google\AdsApi\AdWords\v201809\cm\CustomerFeed[] $value
     */
    public function __construct($ListReturnValueType = null, array $value = null)
    {
        parent::__construct($ListReturnValueType);
        $this->value = $value;
    }
    /**
     * @return \Google\AdsApi\AdWords\v201809\cm\CustomerFeed[]
     */
    public function getValue()
    {
        return $this->value;
    }
    /**
     * @param \Google\AdsApi\AdWords\v201809\cm\CustomerFeed[] $value
     * @return \Google\AdsApi\AdWords\v201809\cm\CustomerFeedReturnValue
     */
    public function setValue(array $value)
    {
        $this->value = $value;
        return $this;
    }
}

?>