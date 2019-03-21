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
namespace Google\AdsApi\AdManager\v201805;

/**
 * This file was generated from WSDL. DO NOT EDIT.
 */
class DateValue extends \Google\AdsApi\AdManager\v201805\Value
{
    /**
     * @var \Google\AdsApi\AdManager\v201805\Date $value
     */
    protected $value = null;
    /**
     * @param \Google\AdsApi\AdManager\v201805\Date $value
     */
    public function __construct($value = null)
    {
        $this->value = $value;
    }
    /**
     * @return \Google\AdsApi\AdManager\v201805\Date
     */
    public function getValue()
    {
        return $this->value;
    }
    /**
     * @param \Google\AdsApi\AdManager\v201805\Date $value
     * @return \Google\AdsApi\AdManager\v201805\DateValue
     */
    public function setValue($value)
    {
        $this->value = $value;
        return $this;
    }
}

?>