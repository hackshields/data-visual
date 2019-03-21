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
class ProductOfferId extends \Google\AdsApi\AdWords\v201809\cm\ProductDimension
{
    /**
     * @var string $value
     */
    protected $value = null;
    /**
     * @param string $ProductDimensionType
     * @param string $value
     */
    public function __construct($ProductDimensionType = null, $value = null)
    {
        parent::__construct($ProductDimensionType);
        $this->value = $value;
    }
    /**
     * @return string
     */
    public function getValue()
    {
        return $this->value;
    }
    /**
     * @param string $value
     * @return \Google\AdsApi\AdWords\v201809\cm\ProductOfferId
     */
    public function setValue($value)
    {
        $this->value = $value;
        return $this;
    }
}

?>